from pathlib import Path
import pandas as pd
import matplotlib.pyplot as plt
from sklearn.metrics import accuracy_score, classification_report, confusion_matrix, ConfusionMatrixDisplay
from nltk.sentiment.vader import SentimentIntensityAnalyzer
import nltk

DATASET = Path("caregiver_notes_dataset.csv")
OUTPUT = Path("vader_predictions.csv")
LABELS = ["negative", "neutral", "positive"]

nltk.download("vader_lexicon", quiet=True)
analyzer = SentimentIntensityAnalyzer()


def classify(note):
    compound = analyzer.polarity_scores(str(note))["compound"]
    if compound <= -0.05:
        return "negative"
    if compound >= 0.05:
        return "positive"
    return "neutral"


def main():
    if not DATASET.exists():
        raise SystemExit(
            "Missing caregiver_notes_dataset.csv. Copy your synthetic CSV into this folder."
        )

    data = pd.read_csv(DATASET)
    required = {"visit_note", "true_label"}
    missing = required.difference(data.columns)
    if missing:
        raise SystemExit(f"Dataset is missing required columns: {', '.join(sorted(missing))}")

    data = data.dropna(subset=["visit_note", "true_label"]).copy()
    data["true_label"] = data["true_label"].astype(str).str.strip().str.lower()
    data["predicted"] = data["visit_note"].apply(classify)
    data["compound_score"] = data["visit_note"].apply(
        lambda note: round(analyzer.polarity_scores(str(note))["compound"], 4)
    )
    data["correct"] = data["true_label"] == data["predicted"]
    data.to_csv(OUTPUT, index=False)

    print(f"Records evaluated: {len(data)}")
    print(f"Accuracy: {accuracy_score(data['true_label'], data['predicted']):.2%}")
    print("\nClassification report:")
    print(classification_report(
        data["true_label"], data["predicted"], labels=LABELS, zero_division=0
    ))

    matrix = confusion_matrix(data["true_label"], data["predicted"], labels=LABELS)
    display = ConfusionMatrixDisplay(confusion_matrix=matrix, display_labels=LABELS)
    display.plot(values_format="d")
    plt.title("VADER Confusion Matrix - Caregiver Visit Notes")
    plt.tight_layout()
    plt.savefig("vader_confusion_matrix.png", dpi=150)
    print(f"\nSaved predictions to {OUTPUT}")
    print("Saved confusion matrix to vader_confusion_matrix.png")


if __name__ == "__main__":
    main()
