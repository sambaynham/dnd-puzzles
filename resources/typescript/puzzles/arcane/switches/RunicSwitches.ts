import p5 from 'p5';
import { P5Component } from '../P5Component';
import { drawWoodenPanel, drawSolvedOverlay, drawInstructions, drawTitle } from '../utils/drawingUtils';
import { ClickEffect, Particle } from '../utils/particles';
import type { SwitchesSolvedDetail } from './types/SwitchesDetail';

export default class RunicSwitches extends P5Component {
    private grid: number[] = [];
    private target: number[] = [];
    private size: number = 3;
    private solved: boolean = false;
    private hinted: boolean = false;
    private moveCount: number = 0;
    private seed: number = 0;
    private clickEffects: ClickEffect[] = [];
    private particles: Particle[] = [];

    // Font Awesome rune codes (shield, bolt, fire, snowflake, moon, eye, hand, heart, skull)
    private runes: string[] = ['\uf132', '\uf0e7', '\uf06d', '\uf2dc', '\uf186', '\uf06e', '\uf256', '\uf004', '\uf54c'];

    static observedAttributes = ['data-solved', 'data-seed'];

    constructor() {
        super();
        this.initializePuzzle();
    }

    private initializePuzzle(): void {
        this.seed = Date.now() % 1000000;
        this.solved = false;
        this.hinted = false;
        this.moveCount = 0;
        this.clickEffects = [];
        this.particles = [];

        // Generate a random target pattern
        this.target = [];
        for (let i = 0; i < this.size * this.size; i++) {
            this.target[i] = Math.random() > 0.5 ? 1 : 0;
        }

        // Start grid as a COPY of target (solved state)
        this.grid = [...this.target];

        // Scramble by applying random toggles (guarantees solvability)
        const numMoves = 5 + Math.floor(Math.random() * 6); // 5-10 random moves
        for (let i = 0; i < numMoves; i++) {
            const idx = Math.floor(Math.random() * this.size * this.size);
            this.applyToggle(idx, this.grid);
        }

        // Ensure at least 3 tiles differ from target
        let attempts = 0;
        while (attempts < 10) {
            let diffCount = 0;
            for (let i = 0; i < this.grid.length; i++) {
                if (this.grid[i] !== this.target[i]) diffCount++;
            }
            if (diffCount >= 3) break;

            this.applyToggle(Math.floor(Math.random() * this.size * this.size), this.grid);
            attempts++;
        }
    }

    private applyToggle(idx: number, arr: number[]): void {
        const r = Math.floor(idx / this.size);
        const c = idx % this.size;
        const offsets = [[0, 0], [1, 0], [-1, 0], [0, 1], [0, -1]];

        for (const o of offsets) {
            const rr = r + o[0];
            const cc = c + o[1];
            if (rr >= 0 && rr < this.size && cc >= 0 && cc < this.size) {
                const i = rr * this.size + cc;
                arr[i] = 1 - arr[i];
            }
        }
    }

    protected setup(p: p5): void {
        p.createCanvas(this.canvasWidth, this.canvasHeight);
        p.textFont('sans-serif');
    }

    protected draw(p: p5): void {
        const w = this.canvasWidth;
        const h = this.canvasHeight;

        p.push();

        // Panel background
        drawWoodenPanel(p, w, h);

        // Title
        drawTitle(p, 'Runic Switches', [200, 160, 100]);

        // Calculate grid dimensions
        const gW = Math.min(w - 40, h - 120);
        const t = gW / (this.size * 1.1);
        const ox = (w - t * this.size) / 2;
        const oy = 60;

        // Draw grid
        for (let row = 0; row < this.size; row++) {
            for (let col = 0; col < this.size; col++) {
                const i = row * this.size + col;
                const gx = ox + col * t;
                const gy = oy + row * t;

                this.drawTile(p, gx, gy, t, i);
            }
        }

        // Draw click effects
        this.clickEffects = this.clickEffects.filter(effect => {
            effect.draw(p);
            return effect.isAlive();
        });

        // Draw particles
        this.particles = this.particles.filter(particle => {
            particle.draw(p);
            return particle.isAlive();
        });

        // Instructions
        drawInstructions(p, 'Click tiles to toggle. Orange glow = target. Bright dot = correct.', w, h);

        // Solved overlay
        if (this.solved) {
            drawSolvedOverlay(p, w, h, this.config.success_message);
        }

        p.pop();
    }

    private drawTile(p: p5, gx: number, gy: number, t: number, i: number): void {
        p.push();
        p.translate(gx, gy);

        const tileW = t - 8;
        const tileH = t - 8;

        // Shadow
        p.noStroke();
        p.fill(0, 0, 0, 60);
        p.rect(4, 4, tileW, tileH, 2);

        // Main stone
        p.fill(65, 60, 55);
        p.stroke(45, 40, 35);
        p.strokeWeight(2);
        p.rect(2, 2, tileW, tileH, 2);

        // Stone texture - subtle noise pattern
        p.noStroke();
        for (let tx = 0; tx < 3; tx++) {
            for (let ty = 0; ty < 3; ty++) {
                const nx = 6 + tx * (tileW / 3);
                const ny = 6 + ty * (tileH / 3);
                p.fill(55 + p.random(-8, 8), 50 + p.random(-8, 8), 45 + p.random(-8, 8), 40);
                p.ellipse(nx, ny, p.random(4, 8), p.random(4, 8));
            }
        }

        // Target indicator (carved groove showing what should be active)
        if (this.target[i]) {
            p.noStroke();
            p.fill(30, 25, 20, 80);
            p.rect(6, 6, tileW - 8, tileH - 8, 2);
        }

        // Active rune (glowing carved symbol)
        if (this.grid[i]) {
            // Ember glow from within
            p.noStroke();
            p.fill(255, 120, 40, 50);
            p.ellipse(tileW / 2 + 2, tileH / 2 + 2, tileW * 0.7);
            p.fill(255, 80, 20, 80);
            p.ellipse(tileW / 2 + 2, tileH / 2 + 2, tileW * 0.5);

            // Glowing rune
            p.fill(255, 160, 60);
            p.textFont('Font Awesome 6 Free');
            p.textStyle(p.BOLD);
            p.textSize(t * 0.35);
            p.textAlign(p.CENTER, p.CENTER);
            p.text(this.runes[i], tileW / 2 + 2, tileH / 2 + 2);
            p.textStyle(p.NORMAL);
        } else {
            // Inactive carved rune (dark, not glowing)
            p.fill(40, 35, 30);
            p.textFont('Font Awesome 6 Free');
            p.textStyle(p.BOLD);
            p.textSize(t * 0.35);
            p.textAlign(p.CENTER, p.CENTER);
            p.text(this.runes[i], tileW / 2 + 2, tileH / 2 + 2);
            p.textStyle(p.NORMAL);
        }

        // Show match/mismatch indicator
        if (this.grid[i] === this.target[i] && this.target[i] === 1) {
            // Correct - golden corner indicator
            p.noStroke();
            p.fill(255, 200, 80, 150);
            p.ellipse(tileW - 6, 10, 6);
        } else if (this.target[i] === 1 && this.grid[i] === 0) {
            // Target wants this on but it's off - dim ember
            p.noStroke();
            p.fill(180, 80, 40, 100);
            p.ellipse(tileW - 6, 10, 6);
        }

        p.pop();
    }

    protected override onMousePressed(p: p5, _event: MouseEvent): void {
        if (this.solved) return;

        const w = this.canvasWidth;
        const h = this.canvasHeight;
        const mx = p.mouseX;
        const my = p.mouseY;

        const gW = Math.min(w - 40, h - 120);
        const t = gW / (this.size * 1.1);
        const ox = (w - t * this.size) / 2;
        const oy = 60;

        for (let row = 0; row < this.size; row++) {
            for (let col = 0; col < this.size; col++) {
                const i = row * this.size + col;
                const gx = ox + col * t;
                const gy = oy + row * t;

                if (mx > gx && mx < gx + t - 8 && my > gy && my < gy + t - 8) {
                    this.applyToggle(i, this.grid);
                    this.moveCount++;
                    this.clickEffects.push(new ClickEffect(gx + (t - 8) / 2, gy + (t - 8) / 2));

                    // Dispatch tile toggled event
                    this.dispatchEvent(new CustomEvent('switches-tile-toggled', {
                        bubbles: true,
                        detail: {
                            tileIndex: i,
                            newState: this.grid[i] === 1,
                            targetState: this.target[i] === 1
                        }
                    }));

                    this.checkSolved(p);
                    return;
                }
            }
        }
    }

    private checkSolved(p: p5): void {
        for (let i = 0; i < this.grid.length; i++) {
            if (this.grid[i] !== this.target[i]) return;
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
        const detail: SwitchesSolvedDetail = {
            seed: this.seed,
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
            // Set one cell to match target
            const diffCells: number[] = [];
            for (let i = 0; i < this.target.length; i++) {
                if (this.grid[i] !== this.target[i]) diffCells.push(i);
            }
            if (diffCells.length > 0) {
                const idx = diffCells[Math.floor(Math.random() * diffCells.length)];
                this.grid[idx] = this.target[idx];
            }
        } else {
            // Additional hint - fix one more cell
            for (let i = 0; i < this.target.length; i++) {
                if (this.grid[i] !== this.target[i]) {
                    this.grid[i] = this.target[i];
                    break;
                }
            }
        }

        if (this.p5Instance) {
            this.checkSolved(this.p5Instance);
        }
    }
}
