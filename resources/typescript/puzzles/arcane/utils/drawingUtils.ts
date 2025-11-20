import p5 from 'p5';

/**
 * Draw a wooden panel background with grain texture
 */
export function drawWoodenPanel(p: p5, w: number, h: number): void {
    // Shadow
    p.noStroke();
    p.fill(0, 0, 0, 80);
    p.rect(4, 4, w, h, 4);

    // Main wooden background
    p.fill(45, 32, 22);
    p.stroke(30, 20, 12);
    p.strokeWeight(3);
    p.rect(0, 0, w, h, 4);

    // Wood grain texture (horizontal lines)
    p.stroke(55, 40, 28, 60);
    p.strokeWeight(1);
    for (let y = 10; y < h - 10; y += 8) {
        const wobble = p.sin(y * 0.1) * 3;
        p.line(8, y, w - 8 + wobble, y + p.sin(y * 0.05) * 2);
    }

    // Iron corner brackets
    drawIronCorner(p, 0, 0, 1, 1);
    drawIronCorner(p, w, 0, -1, 1);
    drawIronCorner(p, 0, h, 1, -1);
    drawIronCorner(p, w, h, -1, -1);

    // Iron studs along edges
    p.fill(60, 55, 50);
    p.stroke(40, 35, 30);
    p.strokeWeight(1);
    for (let x = 30; x < w - 20; x += 40) {
        p.ellipse(x, 8, 6, 6);
        p.ellipse(x, h - 8, 6, 6);
    }
    for (let y = 30; y < h - 20; y += 40) {
        p.ellipse(8, y, 6, 6);
        p.ellipse(w - 8, y, 6, 6);
    }
}

/**
 * Draw an iron corner bracket
 */
export function drawIronCorner(p: p5, x: number, y: number, dirX: number, dirY: number): void {
    p.push();
    p.translate(x, y);

    // Iron bracket
    p.fill(50, 45, 40);
    p.stroke(35, 30, 25);
    p.strokeWeight(2);
    p.beginShape();
    p.vertex(0, 0);
    p.vertex(dirX * 25, 0);
    p.vertex(dirX * 25, dirY * 8);
    p.vertex(dirX * 8, dirY * 8);
    p.vertex(dirX * 8, dirY * 25);
    p.vertex(0, dirY * 25);
    p.endShape(p.CLOSE);

    // Rivet
    p.fill(70, 65, 58);
    p.noStroke();
    p.ellipse(dirX * 12, dirY * 12, 5, 5);

    p.pop();
}

/**
 * Draw a golden glow overlay for solved state
 */
export function drawSolvedOverlay(p: p5, w: number, h: number): void {
    // Golden glow for solved
    p.noStroke();
    p.fill(255, 200, 80, 25);
    p.rect(4, 4, w - 8, h - 8, 4);

    // Golden border glow
    const pulse = p.sin(p.millis() * 0.005) * 0.3 + 0.7;
    p.stroke(255, 180, 60, 150 * pulse);
    p.strokeWeight(3);
    p.noFill();
    p.rect(2, 2, w - 4, h - 4, 4);
}

/**
 * Draw instruction text at the bottom of the panel
 */
export function drawInstructions(p: p5, text: string, w: number, h: number): void {
    p.push();
    p.fill(140, 150, 170);
    p.textSize(10);
    p.textAlign(p.CENTER);
    p.text(text, w / 2, h - 20);
    p.pop();
}

/**
 * Draw a title at the top of the panel
 */
export function drawTitle(p: p5, title: string, color: p5.Color | number[]): void {
    if (Array.isArray(color)) {
        p.fill(color[0], color[1], color[2]);
    } else {
        p.fill(color);
    }
    p.textSize(16);
    p.textStyle(p.BOLD);
    p.text(title, 12, 24);
    p.textStyle(p.NORMAL);
}
