import p5 from 'p5';
import { P5Component } from '../P5Component';
import { drawWoodenPanel, drawSolvedOverlay, drawInstructions, drawTitle } from '../utils/drawingUtils';
import { Particle } from '../utils/particles';
import type { PotionsSolvedDetail } from './types/PotionsDetail';

type Bubble = {
    x: number;
    y: number;
    size: number;
    speed: number;
};

export default class AlchemicalPotions extends P5Component {
    private ingredients: number[][] = []; // RGB values 0-1
    private targetWeights: number[] = [];
    private target: number[] = [];
    private bottles: number[] = [0, 0, 0];
    private solved: boolean = false;
    private hinted: boolean = false;
    private selected: number = -1;
    private bubbles: Bubble[][] = [];
    private particles: Particle[] = [];

    static observedAttributes = ['data-solved'];

    constructor() {
        super();
        this.initializePuzzle();
    }

    private initializePuzzle(): void {
        this.solved = false;
        this.hinted = false;
        this.bottles = [0, 0, 0];
        this.selected = -1;
        this.particles = [];

        // Generate random ingredient colors using HSB-like approach
        this.ingredients = [];
        const hues = [Math.random() * 360, Math.random() * 360, Math.random() * 360];

        for (let i = 0; i < 3; i++) {
            const h = hues[i];
            const r = (Math.sin(h * Math.PI / 180) * 0.5 + 0.5) * 0.8 + 0.2;
            const g = (Math.sin((h + 120) * Math.PI / 180) * 0.5 + 0.5) * 0.8 + 0.2;
            const b = (Math.sin((h + 240) * Math.PI / 180) * 0.5 + 0.5) * 0.8 + 0.2;
            this.ingredients.push([r, g, b]);
        }

        // Generate random target by mixing random proportions
        const a = Math.random() * 0.6 + 0.2;
        const bVal = Math.random() * 0.6 + 0.2;
        const c = Math.random() * 0.6 + 0.2;
        const total = a + bVal + c;
        this.targetWeights = [a / total, bVal / total, c / total];
        this.target = this.mix(this.targetWeights);

        // Create bubbles for each vial
        this.bubbles = [];
        for (let i = 0; i < 3; i++) {
            this.bubbles[i] = [];
            for (let j = 0; j < 5; j++) {
                this.bubbles[i].push({
                    x: Math.random() * 0.6 + 0.2,
                    y: Math.random(),
                    size: Math.random() * 3 + 2,
                    speed: Math.random() * 0.003 + 0.002
                });
            }
        }
    }

    private mix(weights: number[]): number[] {
        const col = [0, 0, 0];
        const total = weights[0] + weights[1] + weights[2];
        if (total === 0) return [0.5, 0.5, 0.5];

        for (let i = 0; i < 3; i++) {
            const w = weights[i] / total;
            col[0] += this.ingredients[i][0] * w;
            col[1] += this.ingredients[i][1] * w;
            col[2] += this.ingredients[i][2] * w;
        }
        return [Math.min(1, col[0]), Math.min(1, col[1]), Math.min(1, col[2])];
    }

    protected setup(p: p5): void {
        p.createCanvas(this.canvasWidth, this.canvasHeight);
    }

    protected draw(p: p5): void {
        const w = this.canvasWidth;
        const h = this.canvasHeight;

        p.push();

        drawWoodenPanel(p, w, h);

        // Title
        drawTitle(p, 'Alchemical Brew', [180, 140, 100]);

        // Target cauldron
        const cauldronSize = Math.min(100, w * 0.25);
        const cauldronX = w - cauldronSize - 20;
        const cauldronY = 50;

        this.drawCauldron(p, cauldronX, cauldronY, cauldronSize, this.target);

        p.fill(160, 140, 120);
        p.textSize(10);
        p.textAlign(p.CENTER);
        p.text('Target Brew', cauldronX + cauldronSize / 2, cauldronY + cauldronSize + 20);

        // Draw ingredient vials
        const vialW = Math.min(60, (w - cauldronSize - 80) / 3);
        const vialH = vialW * 2;

        for (let i = 0; i < 3; i++) {
            const vx = 20 + i * (vialW + 12);
            const vy = 60;
            this.drawVial(p, vx, vy, vialW, vialH, i);
        }

        // Current mixture preview
        const currentMix = this.mix(this.bottles);
        const mixX = w / 2 - 30;
        const mixY = h - 100;

        p.fill(120, 130, 150);
        p.textSize(10);
        p.textAlign(p.CENTER);
        p.text('Your Mixture', mixX + 30, mixY - 8);

        // Small preview cauldron
        this.drawCauldron(p, mixX, mixY, 60, currentMix);

        // Draw particles
        this.particles = this.particles.filter(particle => {
            particle.draw(p);
            return particle.isAlive();
        });

        // Instructions
        drawInstructions(p, 'Click top of vial to remove, bottom to add. Match the target.', w, h);

        if (this.solved) {
            drawSolvedOverlay(p, w, h);
        }

        p.pop();
    }

    private drawCauldron(p: p5, x: number, y: number, size: number, col: number[]): void {
        p.push();
        p.translate(x, y);

        // Cauldron legs
        p.fill(35, 30, 25);
        p.stroke(25, 20, 15);
        p.strokeWeight(2);

        // Left leg
        p.beginShape();
        p.vertex(size * 0.2, size * 0.85);
        p.vertex(size * 0.15, size);
        p.vertex(size * 0.25, size);
        p.vertex(size * 0.3, size * 0.85);
        p.endShape(p.CLOSE);

        // Right leg
        p.beginShape();
        p.vertex(size * 0.7, size * 0.85);
        p.vertex(size * 0.75, size);
        p.vertex(size * 0.85, size);
        p.vertex(size * 0.8, size * 0.85);
        p.endShape(p.CLOSE);

        // Cauldron body - iron pot
        p.stroke(45, 40, 35);
        p.strokeWeight(3);
        p.fill(30, 28, 25);
        p.arc(size / 2, size * 0.6, size * 0.9, size * 0.7, 0, p.PI, p.CHORD);

        // Rim
        p.stroke(55, 50, 45);
        p.strokeWeight(4);
        p.noFill();
        p.arc(size / 2, size * 0.6, size * 0.9, size * 0.2, p.PI, p.TWO_PI);

        // Liquid
        p.noStroke();
        const liquidCol = p.color(col[0] * 255, col[1] * 255, col[2] * 255);

        // Glow from liquid
        p.fill(p.red(liquidCol), p.green(liquidCol), p.blue(liquidCol), 40);
        p.ellipse(size / 2, size * 0.55, size * 0.75, size * 0.35);

        // Main liquid surface
        p.fill(liquidCol);
        p.ellipse(size / 2, size * 0.55, size * 0.6, size * 0.25);

        // Highlight reflection
        p.fill(255, 255, 255, 30);
        p.ellipse(size * 0.38, size * 0.5, size * 0.12, size * 0.06);

        p.pop();
    }

    private drawVial(p: p5, x: number, y: number, w: number, h: number, idx: number): void {
        p.push();
        p.translate(x, y);

        const fillLevel = this.bottles[idx];
        const col = this.ingredients[idx];

        // Cork stopper
        p.fill(120, 90, 60);
        p.stroke(80, 60, 40);
        p.strokeWeight(1);
        p.rect(w * 0.35, -2, w * 0.3, h * 0.08, 2);

        // Bottle neck
        p.fill(50, 60, 55, 180);
        p.stroke(70, 80, 75);
        p.strokeWeight(2);
        p.rect(w * 0.32, h * 0.05, w * 0.36, h * 0.12, 1);

        // Main bottle body - rounded flask shape using vertex for straight segments
        p.beginShape();
        p.vertex(w * 0.32, h * 0.17);
        p.vertex(w * 0.1, h * 0.3);
        p.vertex(w * 0.1, h * 0.7);
        p.vertex(w * 0.1, h * 0.95);
        p.vertex(w * 0.9, h * 0.95);
        p.vertex(w * 0.9, h * 0.7);
        p.vertex(w * 0.9, h * 0.3);
        p.vertex(w * 0.68, h * 0.17);
        p.endShape(p.CLOSE);

        // Glass highlight
        p.noFill();
        p.stroke(120, 130, 125, 60);
        p.strokeWeight(1);
        p.arc(w * 0.3, h * 0.5, w * 0.15, h * 0.4, p.PI * 0.8, p.PI * 1.5);

        // Liquid fill
        if (fillLevel > 0) {
            const liquidH = (h * 0.55) * fillLevel;
            const liquidY = h * 0.85 - liquidH;

            p.noStroke();
            const liquidCol = p.color(col[0] * 255, col[1] * 255, col[2] * 255);

            // Liquid glow
            p.fill(p.red(liquidCol), p.green(liquidCol), p.blue(liquidCol), 60);
            p.ellipse(w / 2, liquidY + liquidH / 2, w * 0.7, liquidH + 8);

            // Main liquid
            p.fill(p.red(liquidCol), p.green(liquidCol), p.blue(liquidCol), 200);
            p.beginShape();
            p.vertex(w * 0.2, liquidY);
            p.vertex(w * 0.15, liquidY + liquidH * 0.5);
            p.vertex(w * 0.15, liquidY + liquidH);
            p.vertex(w * 0.2, liquidY + liquidH);
            p.vertex(w * 0.8, liquidY + liquidH);
            p.vertex(w * 0.85, liquidY + liquidH);
            p.vertex(w * 0.85, liquidY + liquidH * 0.5);
            p.vertex(w * 0.8, liquidY);
            p.endShape(p.CLOSE);

            // Bubbles
            for (const bubble of this.bubbles[idx]) {
                bubble.y -= bubble.speed;
                if (bubble.y < 0) bubble.y = 1;

                const by = liquidY + liquidH * (1 - bubble.y);
                if (by > liquidY && by < liquidY + liquidH) {
                    p.fill(255, 255, 255, 80);
                    p.ellipse(w * 0.3 + (w * 0.4) * bubble.x, by, bubble.size);
                }
            }
        }

        // Ingredient label - wax seal style
        const indicatorCol = p.color(col[0] * 255, col[1] * 255, col[2] * 255);

        // Wax seal
        p.fill(p.red(indicatorCol) * 0.7, p.green(indicatorCol) * 0.7, p.blue(indicatorCol) * 0.7);
        p.stroke(p.red(indicatorCol) * 0.5, p.green(indicatorCol) * 0.5, p.blue(indicatorCol) * 0.5);
        p.strokeWeight(1);
        p.ellipse(w / 2, h + 18, 22, 22);

        // Seal center
        p.fill(indicatorCol);
        p.noStroke();
        p.ellipse(w / 2, h + 18, 14, 14);

        // Selection highlight - golden glow
        if (this.selected === idx) {
            p.noFill();
            p.stroke(255, 200, 80);
            p.strokeWeight(2);
            p.rect(-4, -4, w + 8, h + 32, 4);
        }

        p.pop();
    }

    protected override onMousePressed(p: p5, _event: MouseEvent): void {
        if (this.solved) return;

        const w = this.canvasWidth;
        const mx = p.mouseX;
        const my = p.mouseY;

        const cauldronSize = Math.min(100, w * 0.25);
        const vialW = Math.min(60, (w - cauldronSize - 80) / 3);
        const vialH = vialW * 2;

        // Check vial clicks
        for (let i = 0; i < 3; i++) {
            const vx = 20 + i * (vialW + 12);
            const vy = 60;

            if (mx > vx && mx < vx + vialW && my > vy && my < vy + vialH + 40) {
                // Top half to remove, bottom half to add
                const vialMidY = vy + vialH / 2;
                if (my < vialMidY) {
                    // Top half - remove
                    this.bottles[i] = Math.max(0, this.bottles[i] - 0.2);
                } else {
                    // Bottom half - add
                    this.bottles[i] = Math.min(1, this.bottles[i] + 0.2);
                }

                // Dispatch adjustment event
                this.dispatchEvent(new CustomEvent('potion-adjusted', {
                    bubbles: true,
                    detail: {
                        ingredientIndex: i,
                        newAmount: this.bottles[i],
                        currentMix: this.mix(this.bottles)
                    }
                }));

                this.checkSolved(p);
                return;
            }
        }
    }

    private checkSolved(p: p5): void {
        const total = this.bottles.reduce((a, b) => a + b, 0);
        if (total <= 0) return;

        const currentMix = this.mix(this.bottles);
        const d = Math.sqrt(
            Math.pow(currentMix[0] - this.target[0], 2) +
            Math.pow(currentMix[1] - this.target[1], 2) +
            Math.pow(currentMix[2] - this.target[2], 2)
        );

        if (d < 0.1) {
            this.solved = true;
            this.dataset.solved = 'true';

            // Create celebration particles
            const colors = [
                p.color(100, 255, 180),
                p.color(255, 220, 100),
                p.color(180, 140, 255),
                p.color(100, 200, 255)
            ];

            for (let i = 0; i < 30; i++) {
                this.particles.push(new Particle(
                    p,
                    this.canvasWidth / 2 + p.random(-50, 50),
                    this.canvasHeight / 2 + p.random(-30, 30),
                    p.random(colors)
                ));
            }

            // Dispatch solved event
            const detail: PotionsSolvedDetail = {
                finalMix: currentMix,
                targetMix: this.target,
                accuracy: 1 - d
            };

            this.dispatchEvent(new CustomEvent('puzzle-solved', {
                bubbles: true,
                detail
            }));
        }
    }

    public override reset(): void {
        this.initializePuzzle();
        this.dataset.solved = 'false';
    }

    public override giveHint(): void {
        if (this.solved) return;

        if (!this.hinted) {
            // Add the most needed ingredient
            let best = 0;
            let bestIdx = 0;
            for (let i = 0; i < 3; i++) {
                if (this.targetWeights[i] > best) {
                    best = this.targetWeights[i];
                    bestIdx = i;
                }
            }
            this.bottles[bestIdx] = Math.min(1, this.bottles[bestIdx] + 0.3);
            this.hinted = true;
        } else {
            // Add more of what's needed
            for (let i = 0; i < 3; i++) {
                if (this.targetWeights[i] > 0.2) {
                    this.bottles[i] = Math.min(1, this.bottles[i] + 0.2);
                }
            }
        }

        if (this.p5Instance) {
            this.checkSolved(this.p5Instance);
        }
    }
}
