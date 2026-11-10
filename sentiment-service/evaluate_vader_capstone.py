"""
ElderCare Capstone Project - Chapter 5: System Implementation & Testing
VADER Sentiment Analysis Evaluation Script

This script evaluates the pre-trained NLTK VADER sentiment model on the
caregiver notes dataset (caregiver_notes_dataset.csv) to validate its
clinical alerting capabilities for home-based elder care monitoring.

Evaluations performed:
1. Target Binarization: true_label == 'negative' -> 1 (At-Risk/Alert), others -> 0
2. ROC-AUC Calculation & ROC Curve Plotting (saved to vader_roc_curve.png)
3. Threshold Sensitivity Analysis (-0.05, -0.20, -0.50)
4. Lexicon Impact Analysis & Horizontal Bar Chart (saved to vader_word_impact.png)
5. Comprehensive 3-Class Classification Report (positive, neutral, negative)
"""

import os
import sys
import tempfile
from pathlib import Path
import re
from collections import Counter

# Set Matplotlib cache to a writable temp directory before importing pyplot
os.environ["MPLCONFIGDIR"] = os.path.join(tempfile.gettempdir(), "matplotlib_eldercare")

import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
import seaborn as sns
from sklearn.metrics import (
    classification_report,
    roc_curve,
    roc_auc_score,
    precision_score,
    recall_score,
    f1_score,
    confusion_matrix
)
import nltk
from nltk.sentiment.vader import SentimentIntensityAnalyzer

# Initialize VADER
try:
    nltk.download("vader_lexicon", quiet=True)
except Exception:
    pass

analyzer = SentimentIntensityAnalyzer()

# Define paths
SCRIPT_DIR = Path(__file__).resolve().parent
POSSIBLE_DATA_PATHS = [
    Path("caregiver_notes_dataset.csv"),
    SCRIPT_DIR / "caregiver_notes_dataset.csv",
    SCRIPT_DIR.parent / "caregiver_notes_dataset.csv",
    SCRIPT_DIR.parent.parent / "caregiver_notes_dataset.csv",
    Path("eldercare/sentiment-service/caregiver_notes_dataset.csv"),
]


def load_dataset() -> pd.DataFrame:
    """Locate and load the caregiver notes dataset."""
    data_path = None
    for p in POSSIBLE_DATA_PATHS:
        if p.exists():
            data_path = p
            break

    if not data_path:
        raise FileNotFoundError(
            "Could not locate 'caregiver_notes_dataset.csv'. "
            "Please ensure the file is in the script directory or project root."
        )

    print(f"[1/5] Loading dataset from: {data_path.resolve()}")
    df = pd.read_csv(data_path)
    
    # Standardize column names
    col_map = {}
    for col in df.columns:
        if col.strip().lower() in ["visit_note", "note", "caregiver_note", "text"]:
            col_map[col] = "visit_note"
        elif col.strip().lower() in ["true_label", "label", "sentiment"]:
            col_map[col] = "true_label"
    df = df.rename(columns=col_map)

    if "visit_note" not in df.columns or "true_label" not in df.columns:
        raise ValueError(f"Dataset must contain 'visit_note' and 'true_label'. Found: {list(df.columns)}")

    df = df.dropna(subset=["visit_note", "true_label"]).copy()
    df["true_label"] = df["true_label"].astype(str).str.strip().str.lower()
    print(f"      Total records loaded: {len(df)}")
    print(f"      Class distribution:\n{df['true_label'].value_counts().to_string(header=False)}\n")
    return df


def classify_vader_3class(compound: float, neg_th: float = -0.05, pos_th: float = 0.05) -> str:
    """Standard 3-class VADER categorization."""
    if compound <= neg_th:
        return "negative"
    if compound >= pos_th:
        return "positive"
    return "neutral"


def run_roc_auc_evaluation(df: pd.DataFrame, output_image: str = "vader_roc_curve.png"):
    """
    1. Binarize Target: true_label == 'negative' -> 1 (At-Risk/Alert), others -> 0
    2. Compute ROC-AUC using inverted VADER compound score (-1 * compound_score)
    3. Plot and save high-resolution ROC Curve.
    """
    print("[2/5] Running ROC-AUC Evaluation on At-Risk / Negative Observations...")
    
    # Binarize Target
    df["is_negative"] = (df["true_label"] == "negative").astype(int)
    
    # Prediction probability / risk score:
    # High negative compound score (e.g. -0.80) becomes high alert probability (+0.80)
    df["inverted_compound"] = -1.0 * df["compound_score"]
    
    # Compute ROC Curve and ROC-AUC
    fpr, tpr, thresholds = roc_curve(df["is_negative"], df["inverted_compound"])
    auc_score = roc_auc_score(df["is_negative"], df["inverted_compound"])
    
    print(f"      Calculated ROC-AUC Score: {auc_score:.4f} ({auc_score*100:.2f}%)")

    # Plotting ROC Curve
    sns.set_theme(style="whitegrid", font="sans-serif")
    plt.figure(figsize=(8, 6), dpi=300)
    
    plt.plot(fpr, tpr, color="#2563eb", lw=2.5, label=f"VADER Model (AUC = {auc_score:.3f})")
    plt.plot([0, 1], [0, 1], color="#94a3b8", lw=1.5, linestyle="--", label="Random Classifier (AUC = 0.500)")
    
    # Highlight the standard threshold point (-0.05 in compound score -> +0.05 inverted)
    std_inverted_th = 0.05
    # Find closest threshold index
    th_idx = np.argmin(np.abs(thresholds - std_inverted_th))
    plt.scatter(
        fpr[th_idx],
        tpr[th_idx],
        color="#dc2626",
        s=90,
        zorder=5,
        label=f"Operational Threshold (-0.05)\n(FPR = {fpr[th_idx]:.2f}, Recall/TPR = {tpr[th_idx]:.2f})"
    )
    
    plt.xlim([-0.02, 1.02])
    plt.ylim([0.0, 1.05])
    plt.xlabel("False Positive Rate (1 - Specificity)", fontsize=11, fontweight="bold", labelpad=8)
    plt.ylabel("True Positive Rate (Sensitivity / Recall)", fontsize=11, fontweight="bold", labelpad=8)
    plt.title("ROC Curve for Caregiver Note Alert Detection\n(Class: Negative / At-Risk)", fontsize=13, fontweight="bold", pad=12)
    plt.legend(loc="lower right", frameon=True, facecolor="#ffffff", edgecolor="#cbd5e1", fontsize=10)
    plt.tight_layout()
    
    plt.savefig(output_image, dpi=300)
    plt.close()
    print(f"      Saved ROC Curve chart to: {output_image}\n")
    return auc_score


def run_threshold_sensitivity_analysis(df: pd.DataFrame) -> pd.DataFrame:
    """
    Evaluate how changing the negative classification threshold from -0.05 to -0.20 and -0.50
    impacts Precision, Recall, and F1-score for the 'negative' class.
    """
    print("[3/5] Performing Threshold Sensitivity Analysis for 'negative' class...")
    thresholds = [-0.05, -0.20, -0.50]
    y_true = df["is_negative"]
    
    records = []
    for th in thresholds:
        # Binary prediction: note is flagged negative if compound <= threshold
        y_pred = (df["compound_score"] <= th).astype(int)
        
        prec = precision_score(y_true, y_pred, zero_division=0)
        rec = recall_score(y_true, y_pred, zero_division=0)
        f1 = f1_score(y_true, y_pred, zero_division=0)
        pred_count = int(y_pred.sum())
        
        # Calculate True Positives, False Positives, False Negatives
        cm = confusion_matrix(y_true, y_pred)
        tn, fp, fn, tp = cm.ravel()
        specificity = tn / (tn + fp) if (tn + fp) > 0 else 0.0

        records.append({
            "Negative Threshold": f"compound <= {th:.2f}",
            "Threshold (Value)": th,
            "Precision": round(prec, 4),
            "Recall (Sensitivity)": round(rec, 4),
            "F1-Score": round(f1, 4),
            "Specificity": round(specificity, 4),
            "Flagged Alerts (TP+FP)": pred_count,
            "Missed Alerts (FN)": fn
        })
        
    sensitivity_df = pd.DataFrame(records)
    print("\n" + "=" * 95)
    print("                      THRESHOLD SENSITIVITY ANALYSIS (NEGATIVE CLASS)")
    print("=" * 95)
    print(sensitivity_df[["Negative Threshold", "Precision", "Recall (Sensitivity)", "F1-Score", "Specificity", "Flagged Alerts (TP+FP)", "Missed Alerts (FN)"]].to_string(index=False))
    print("=" * 95 + "\n")
    return sensitivity_df


def run_lexicon_impact_analysis(df: pd.DataFrame, output_image: str = "vader_word_impact.png"):
    """
    Identify specific words in the dataset's negative notes that contributed the most
    to the negative sentiment scores by cross-referencing with the VADER lexicon.
    Plot a clean horizontal bar chart of the Top 10 most impactful negative words.
    """
    print("[4/5] Running Lexicon Impact Analysis on Negative Clinical Notes...")
    
    # Filter for notes that are labeled negative
    neg_notes = df[df["true_label"] == "negative"]["visit_note"]
    
    cumulative_impact = Counter()
    word_frequencies = Counter()
    word_valence = {}
    
    for note in neg_notes:
        # Extract alphanumeric words
        words = re.findall(r"[a-zA-Z]+", str(note).lower())
        for word in words:
            if word in analyzer.lexicon and analyzer.lexicon[word] < 0:
                val = analyzer.lexicon[word]
                cumulative_impact[word] += abs(val)
                word_frequencies[word] += 1
                word_valence[word] = val

    # Top 10 words by cumulative negative score impact
    top10 = cumulative_impact.most_common(10)
    top_words = [item[0] for item in top10]
    top_impacts = [item[1] for item in top10]
    top_freqs = [word_frequencies[w] for w in top_words]
    top_valences = [word_valence[w] for w in top_words]

    impact_table = pd.DataFrame({
        "Word": top_words,
        "Lexicon Valence": [f"{v:.2f}" for v in top_valences],
        "Frequency in Negative Notes": top_freqs,
        "Cumulative Impact Score": [f"{imp:.2f}" for imp in top_impacts]
    })
    
    print("\n" + "-" * 75)
    print("                 TOP 10 MOST IMPACTFUL NEGATIVE WORDS")
    print("-" * 75)
    print(impact_table.to_string(index=False))
    print("-" * 75 + "\n")

    # Plot Horizontal Bar Chart
    sns.set_theme(style="whitegrid", font="sans-serif")
    plt.figure(figsize=(9, 6), dpi=300)
    
    # Invert order for intuitive top-down display in horizontal bar chart
    plot_df = pd.DataFrame({
        "Word": top_words[::-1],
        "Impact": top_impacts[::-1],
        "Freq": top_freqs[::-1],
        "Valence": top_valences[::-1]
    })
    
    # Use a refined red-to-amber palette
    colors = sns.color_palette("flare", n_colors=10)
    bars = plt.barh(plot_df["Word"], plot_df["Impact"], color=colors, edgecolor="#475569", height=0.65)
    
    # Add data annotations on bars
    for bar, freq, val in zip(bars, plot_df["Freq"], plot_df["Valence"]):
        width = bar.get_width()
        plt.text(
            width + 0.2,
            bar.get_y() + bar.get_height() / 2,
            f"{width:.1f}  (n={freq}, valence={val})",
            va="center",
            ha="left",
            fontsize=9,
            fontweight="semibold",
            color="#1e293b"
        )
        
    plt.xlim(0, max(top_impacts) * 1.28)
    plt.xlabel("Cumulative Lexical Impact Score (|Valence| × Frequency)", fontsize=11, fontweight="bold", labelpad=8)
    plt.title("VADER Lexicon Impact Analysis:\nTop 10 Clinical Sentiment Drivers in Negative Caregiver Notes", fontsize=12, fontweight="bold", pad=12)
    plt.tight_layout()
    
    plt.savefig(output_image, dpi=300)
    plt.close()
    print(f"      Saved Lexicon Impact chart to: {output_image}\n")
    return impact_table


def run_classification_report(df: pd.DataFrame):
    """
    Print the standard 3-class classification report (precision, recall, f1)
    for positive, neutral, and negative classes.
    """
    print("[5/5] Generating Standard 3-Class Classification Report...")
    labels = ["negative", "neutral", "positive"]
    
    report_dict = classification_report(
        df["true_label"],
        df["predicted_label"],
        labels=labels,
        output_dict=True,
        zero_division=0
    )
    report_str = classification_report(
        df["true_label"],
        df["predicted_label"],
        labels=labels,
        digits=4,
        zero_division=0
    )
    
    print("\n" + "=" * 65)
    print("             VADER 3-CLASS CLASSIFICATION REPORT")
    print("=" * 65)
    print(report_str)
    print("=" * 65 + "\n")
    return report_dict


def main():
    print("\n" + "=" * 70)
    print(" ELDERCARE CAPSTONE: CHAPTER 5 VADER EVALUATION PIPELINE")
    print("=" * 70)
    
    # 1. Load Data
    df = load_dataset()
    
    # 2. Calculate VADER compound scores and default 3-class predictions
    df["compound_score"] = df["visit_note"].apply(
        lambda note: round(float(analyzer.polarity_scores(str(note))["compound"]), 4)
    )
    df["predicted_label"] = df["compound_score"].apply(classify_vader_3class)
    
    # 3. Output filenames
    roc_chart_name = "vader_roc_curve.png"
    word_chart_name = "vader_word_impact.png"
    
    # 4. ROC-AUC Analysis
    auc = run_roc_auc_evaluation(df, output_image=roc_chart_name)
    
    # 5. Threshold Sensitivity Analysis
    sensitivity_table = run_threshold_sensitivity_analysis(df)
    
    # 6. Lexicon Impact Analysis
    impact_table = run_lexicon_impact_analysis(df, output_image=word_chart_name)
    
    # 7. Standard Classification Report
    report = run_classification_report(df)
    
    print("Evaluation completed successfully!")
    print(f"Generated artifacts: {roc_chart_name}, {word_chart_name}")


if __name__ == "__main__":
    main()
