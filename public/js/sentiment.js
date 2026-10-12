(() => {
    const data = window.elderCareSentimentData;
    const canvas = document.getElementById("sentimentTrendChart");
    const emptyMessage = document.getElementById("trendEmpty");

    if (!canvas || !data || !Array.isArray(data.labels) || !Array.isArray(data.scores)) {
        return;
    }

    const points = data.scores.filter(value => value !== null && value !== undefined);
    if (points.length === 0) {
        emptyMessage.hidden = false;
        canvas.hidden = true;
        return;
    }

    const ctx = canvas.getContext("2d");
    const css = getComputedStyle(document.querySelector(".sentiment-page"));
    const lineColor = css.getPropertyValue("--sentiment-blue").trim() || "#087ee8";
    const gridColor = "#e8edf3";
    const textColor = "#7a8492";
    const minY = -1;
    const maxY = 1;
    const pad = { top: 15, right: 16, bottom: 35, left: 38 };

    function draw() {
        const ratio = window.devicePixelRatio || 1;
        const width = canvas.clientWidth;
        const height = canvas.clientHeight;
        if (!width || !height) return;

        canvas.width = Math.round(width * ratio);
        canvas.height = Math.round(height * ratio);
        ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
        ctx.clearRect(0, 0, width, height);

        const chartWidth = width - pad.left - pad.right;
        const chartHeight = height - pad.top - pad.bottom;
        const x = index => pad.left + (data.labels.length < 2 ? chartWidth / 2 : index * chartWidth / (data.labels.length - 1));
        const y = value => pad.top + ((maxY - value) / (maxY - minY)) * chartHeight;

        ctx.font = "11px system-ui, sans-serif";
        ctx.textAlign = "right";
        ctx.textBaseline = "middle";

        [-1, -0.5, 0, 0.5, 1].forEach(value => {
            const yy = y(value);
            ctx.beginPath();
            ctx.strokeStyle = gridColor;
            ctx.lineWidth = 1;
            ctx.moveTo(pad.left, yy);
            ctx.lineTo(width - pad.right, yy);
            ctx.stroke();
            ctx.fillStyle = textColor;
            ctx.fillText(value.toFixed(1), pad.left - 8, yy);
        });

        ctx.textAlign = "center";
        ctx.textBaseline = "top";
        data.labels.forEach((label, index) => {
            ctx.fillStyle = textColor;
            ctx.fillText(label, x(index), height - pad.bottom + 12);
        });

        let activePath = false;
        ctx.beginPath();
        ctx.strokeStyle = lineColor;
        ctx.lineWidth = 2.5;
        ctx.lineJoin = "round";
        ctx.lineCap = "round";

        data.scores.forEach((value, index) => {
            if (value === null || value === undefined) {
                activePath = false;
                return;
            }
            if (!activePath) {
                ctx.moveTo(x(index), y(value));
                activePath = true;
            } else {
                ctx.lineTo(x(index), y(value));
            }
        });
        ctx.stroke();

        data.scores.forEach((value, index) => {
            if (value === null || value === undefined) return;
            ctx.beginPath();
            ctx.fillStyle = lineColor;
            ctx.arc(x(index), y(value), 4, 0, Math.PI * 2);
            ctx.fill();
            ctx.strokeStyle = "#ffffff";
            ctx.lineWidth = 2;
            ctx.stroke();
        });
    }

    draw();
    window.addEventListener("resize", draw);
})();
