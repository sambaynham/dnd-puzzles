import p5 from 'p5';
import { P5Component } from '../P5Component';
import { drawWoodenPanel, drawSolvedOverlay, drawInstructions, drawTitle } from '../utils/drawingUtils';
import { Particle, RingClickEffect } from '../utils/particles';
import type { RingsSolvedDetail } from './types/RingsDetail';

export default class ArcaneRings extends P5Component {
    private rings: number = 3;
    private sections: number = 6;
    private state: number[] = [];
    private target: number[] = [];
    private visualRotation: number[] = [];
    private targetRotation: number[] = [];
    private solved: boolean = false;
    private hinted: boolean = false;
    private moveCount: number = 0;
    private clickEffects: RingClickEffect[] = [];
    private particles: Particle[] = [];

    // Font Awesome sigils (gem, star, sun)
    private sigils: string[] = ['\uf3a5', '\uf005', '\uf185'];

    static observedAttributes = ['data-solved'];

    constructor() {
        super();
        this.initializePuzzle();
    }

    private initializePuzzle(): void {
        this.solved = false;
        this.hinted = false;
        this.moveCount = 0;
        this.clickEffects = [];
        this.particles = [];

        this.state = [];
        this.target = [];
        this.visualRotation = [];
        this.targetRotation = [];

        for (let r = 0; r < this.rings; r++) {
            // Target is always 0 (aligned at top)
            this.target[r] = 0;
            // Randomize starting position (but not 0, so it's not already solved)
            this.state[r] = 1 + Math.floor(Math.random() * (this.sections - 1));
            // Initialize visual rotation to match state
            this.visualRotation[r] = this.state[r] * (Math.PI * 2 / this.sections);
            this.targetRotation[r] = this.visualRotation[r];
        }
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
        drawTitle(p, 'Arcane Rings', [180, 160, 120]);

        const cx = w / 2;
        const cy = h / 2 + 20;
        const maxR = Math.min(w, h - 80) / 2.5;

        // Draw alignment marker at top - iron arrow
        p.push();
        p.translate(cx, cy);

        // Iron mounting plate
        p.fill(50, 45, 40);
        p.stroke(35, 30, 25);
        p.strokeWeight(2);
        p.ellipse(0, -maxR - 12, 20, 20);

        // Golden arrow indicator
        p.fill(180, 150, 60);
        p.stroke(120, 100, 40);
        p.strokeWeight(1);
        p.triangle(-6, -maxR - 18, 6, -maxR - 18, 0, -maxR - 6);

        p.pop();

        // Animate rotations with easing
        for (let r = 0; r < this.rings; r++) {
            const diff = this.targetRotation[r] - this.visualRotation[r];
            this.visualRotation[r] += diff * 0.12;
        }

        // Draw rings from outside to inside
        for (let r = 0; r < this.rings; r++) {
            const outerR = maxR * (1 - r * 0.3);
            const innerR = outerR * 0.7;

            // Stone colors - different stone types
            const stoneBase = [
                [75, 70, 65],   // Outer - dark granite
                [85, 80, 70],   // Middle - sandstone
                [70, 75, 70]    // Inner - slate
            ][r];

            const runeGlow = [
                p.color(255, 160, 60),   // Outer - amber
                p.color(180, 220, 255),  // Middle - ice blue
                p.color(160, 255, 160)   // Inner - emerald
            ][r];

            for (let s = 0; s < this.sections; s++) {
                p.push();
                p.translate(cx, cy);

                const rotation = this.visualRotation[r];
                const angle = (p.TWO_PI / this.sections) * s - p.PI / 2 + rotation;
                const nextAngle = angle + p.TWO_PI / this.sections;

                // Draw stone section
                p.stroke(stoneBase[0] - 20, stoneBase[1] - 20, stoneBase[2] - 20);
                p.strokeWeight(3);
                p.fill(stoneBase[0], stoneBase[1], stoneBase[2]);

                p.beginShape();
                for (let a = angle + 0.02; a <= nextAngle - 0.02; a += 0.1) {
                    p.vertex(p.cos(a) * innerR, p.sin(a) * innerR);
                }
                for (let a = nextAngle - 0.02; a >= angle + 0.02; a -= 0.1) {
                    p.vertex(p.cos(a) * outerR, p.sin(a) * outerR);
                }
                p.endShape(p.CLOSE);

                // Stone texture
                p.noStroke();
                for (let i = 0; i < 3; i++) {
                    const ta = angle + p.random(0.1, (nextAngle - angle) - 0.1);
                    const tr = p.random(innerR + 5, outerR - 5);
                    p.fill(stoneBase[0] + p.random(-15, 15), stoneBase[1] + p.random(-15, 15), stoneBase[2] + p.random(-15, 15), 60);
                    p.ellipse(p.cos(ta) * tr, p.sin(ta) * tr, p.random(3, 6), p.random(3, 6));
                }

                // Highlight edge
                p.stroke(stoneBase[0] + 20, stoneBase[1] + 20, stoneBase[2] + 20, 80);
                p.strokeWeight(1);
                p.noFill();
                p.arc(0, 0, outerR * 2, outerR * 2, angle + 0.05, angle + 0.3);

                // Draw sigil on section 0
                if (s === 0) {
                    const midAngle = angle + (p.TWO_PI / this.sections) / 2;
                    const midR = (innerR + outerR) / 2;
                    const sx = p.cos(midAngle) * midR;
                    const sy = p.sin(midAngle) * midR;

                    // Check if aligned (at top)
                    const isAligned = Math.abs(midAngle + p.PI / 2) < 0.1 || Math.abs(midAngle + p.PI / 2 - p.TWO_PI) < 0.1;

                    if (isAligned) {
                        // Glowing rune
                        p.noStroke();
                        p.fill(p.red(runeGlow), p.green(runeGlow), p.blue(runeGlow), 80);
                        p.ellipse(sx, sy, 28);
                        p.fill(p.red(runeGlow), p.green(runeGlow), p.blue(runeGlow), 120);
                        p.ellipse(sx, sy, 18);
                        p.fill(runeGlow);
                    } else {
                        // Dormant carved rune
                        p.fill(stoneBase[0] - 25, stoneBase[1] - 25, stoneBase[2] - 25);
                        p.textFont('Font Awesome 6 Free');
                        p.textStyle(p.BOLD);
                        p.textSize(14);
                        p.textAlign(p.CENTER, p.CENTER);
                        p.text(this.sigils[r], sx + 1, sy + 1);

                        p.fill(stoneBase[0] + 15, stoneBase[1] + 15, stoneBase[2] + 15);
                    }

                    p.textFont('Font Awesome 6 Free');
                    p.textStyle(p.BOLD);
                    p.textSize(14);
                    p.textAlign(p.CENTER, p.CENTER);
                    p.text(this.sigils[r], sx, sy);
                    p.textStyle(p.NORMAL);
                }

                p.pop();
            }
        }

        // Center stone hub
        p.push();
        p.translate(cx, cy);
        p.fill(55, 50, 45);
        p.stroke(40, 35, 30);
        p.strokeWeight(3);
        p.ellipse(0, 0, maxR * 0.35);

        // Center emblem
        p.fill(70, 65, 58);
        p.noStroke();
        p.ellipse(0, 0, maxR * 0.2);
        p.pop();

        // Draw click effects
        this.clickEffects = this.clickEffects.filter(effect => {
            effect.draw(p, cx, cy);
            return effect.isAlive();
        });

        // Draw particles
        this.particles = this.particles.filter(particle => {
            particle.draw(p);
            return particle.isAlive();
        });

        // Instructions
        drawInstructions(p, 'Click rings to rotate. Align all sigils to the golden marker.', w, h);

        if (this.solved) {
            drawSolvedOverlay(p, w, h);
        }

        p.pop();
    }

    protected override onMousePressed(p: p5, _event: MouseEvent): void {
        if (this.solved) return;

        const w = this.canvasWidth;
        const h = this.canvasHeight;
        const mx = p.mouseX;
        const my = p.mouseY;

        const cx = w / 2;
        const cy = h / 2 + 20;
        const maxR = Math.min(w, h - 80) / 2.5;

        const dx = mx - cx;
        const dy = my - cy;
        const d = Math.sqrt(dx * dx + dy * dy);

        // Check which ring was clicked
        for (let r = 0; r < this.rings; r++) {
            const outerR = maxR * (1 - r * 0.3);
            const innerR = outerR * 0.7;

            if (d >= innerR && d <= outerR) {
                // Rotate this ring
                this.state[r] = (this.state[r] + 1) % this.sections;
                this.targetRotation[r] += p.TWO_PI / this.sections;
                this.moveCount++;
                this.clickEffects.push(new RingClickEffect((innerR + outerR) / 2));

                // Dispatch rotation event
                this.dispatchEvent(new CustomEvent('ring-rotated', {
                    bubbles: true,
                    detail: {
                        ringIndex: r,
                        newPosition: this.state[r],
                        isAligned: this.state[r] === this.target[r]
                    }
                }));

                this.checkSolved(p);
                return;
            }
        }
    }

    private checkSolved(p: p5): void {
        for (let r = 0; r < this.rings; r++) {
            if (this.state[r] !== this.target[r]) return;
        }

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
        const detail: RingsSolvedDetail = {
            moveCount: this.moveCount
        };

        this.dispatchEvent(new CustomEvent('puzzle-solved', {
            bubbles: true,
            detail
        }));
    }

    public override reset(): void {
        this.initializePuzzle();
        this.dataset.solved = 'false';
    }

    public override giveHint(): void {
        if (this.solved) return;

        if (!this.hinted) {
            this.hinted = true;
            // Rotate one ring closer to solution
            for (let r = 0; r < this.rings; r++) {
                if (this.state[r] !== this.target[r]) {
                    const stepsNeeded = (this.target[r] - this.state[r] + this.sections) % this.sections;
                    let stepsToMove: number;
                    if (stepsNeeded > this.sections / 2) {
                        stepsToMove = 1;
                    } else {
                        stepsToMove = Math.min(2, stepsNeeded);
                    }
                    this.state[r] = (this.state[r] + stepsToMove) % this.sections;
                    this.targetRotation[r] += stepsToMove * (Math.PI * 2 / this.sections);
                    break;
                }
            }
        } else {
            // Solve one ring completely
            for (let r = 0; r < this.rings; r++) {
                if (this.state[r] !== this.target[r]) {
                    const stepsNeeded = (this.target[r] - this.state[r] + this.sections) % this.sections;
                    this.state[r] = this.target[r];
                    this.targetRotation[r] += stepsNeeded * (Math.PI * 2 / this.sections);
                    break;
                }
            }
        }

        if (this.p5Instance) {
            this.checkSolved(this.p5Instance);
        }
    }
}
