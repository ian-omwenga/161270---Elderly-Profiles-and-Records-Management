import os
from flask import Flask, jsonify, request
import nltk
from nltk.sentiment.vader import SentimentIntensityAnalyzer

app = Flask(__name__)


nltk.download("vader_lexicon", quiet=True)
analyzer = SentimentIntensityAnalyzer()


def classify_compound(compound_score: float) -> str:
    if compound_score <= -0.05:
        return "negative"
    if compound_score >= 0.05:
        return "positive"
    return "neutral"


@app.get("/health")
def health():
    return jsonify({"status": "ok", "model": "NLTK VADER"})


@app.post("/analyze")
def analyze():
    payload = request.get_json(silent=True) or {}
    note = payload.get("note")

    if not isinstance(note, str) or not note.strip():
        return jsonify({"error": "A non-empty note string is required."}), 400

    scores = analyzer.polarity_scores(note.strip())
    compound = round(float(scores["compound"]), 4)

    return jsonify({
        "positive_score": round(float(scores["pos"]), 4),
        "neutral_score": round(float(scores["neu"]), 4),
        "negative_score": round(float(scores["neg"]), 4),
        "compound_score": compound,
        "classification": classify_compound(compound),
        "flagged": classify_compound(compound) == "negative"
    })


if __name__ == "__main__":
    host = os.getenv("FLASK_HOST", "127.0.0.1")
    port = int(os.getenv("FLASK_PORT", "5001"))
    app.run(host=host, port=port, debug=False)
