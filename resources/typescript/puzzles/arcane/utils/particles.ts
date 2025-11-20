import p5 from 'p5';

/**
 * Celebration particle for puzzle completion
 */
export class Particle {
    x: number;
    y: number;
    vx: number;
    vy: number;
    age: number = 0;
    col: p5.Color;
    size: number;
    rotation: number;
    rotSpeed: number;

    constructor(p: p5, x: number, y: number, col?: p5.Color) {
        this.x = x;
        this.y = y;
        this.vx = p.random(-3, 3);
        this.vy = p.random(-5, -2);
        this.col = col || p.color(180, 255, 200);
        this.size = p.random(3, 8);
        this.rotation = p.random(p.TWO_PI);
        this.rotSpeed = p.random(-0.1, 0.1);
    }

    draw(p: p5): void {
        this.vy += 0.08;
        this.x += this.vx;
        this.y += this.vy;
        this.age++;
        this.rotation += this.rotSpeed;

        const alpha = p.max(0, 255 - this.age * 2);
        p.push();
        p.translate(this.x, this.y);
        p.rotate(this.rotation);
        p.noStroke();

        // Glow
        p.fill(p.red(this.col), p.green(this.col), p.blue(this.col), alpha * 0.3);
        p.ellipse(0, 0, this.size * 3);

        // Core - star shape
        p.fill(p.red(this.col), p.green(this.col), p.blue(this.col), alpha);
        p.beginShape();
        for (let i = 0; i < 5; i++) {
            let angle = (p.TWO_PI / 5) * i - p.PI / 2;
            let r = this.size;
            p.vertex(p.cos(angle) * r, p.sin(angle) * r);
            angle += p.TWO_PI / 10;
            r = this.size * 0.4;
            p.vertex(p.cos(angle) * r, p.sin(angle) * r);
        }
        p.endShape(p.CLOSE);
        p.pop();
    }

    isAlive(): boolean {
        return this.age < 120;
    }
}

/**
 * Floating ambient particle for background effects
 */
export class FloatingParticle {
    x: number;
    y: number;
    vx: number;
    vy: number;
    size: number;
    hue: p5.Color;
    life: number;
    age: number = 0;
    private p: p5;

    constructor(p: p5, width: number, height: number) {
        this.p = p;
        this.x = p.random(width);
        this.y = p.random(height);
        this.vx = p.random(-0.3, 0.3);
        this.vy = p.random(-0.5, -0.1);
        this.size = p.random(2, 6);
        this.hue = p.random([
            p.color(120, 180, 255, 60),  // Blue
            p.color(180, 120, 255, 60),  // Purple
            p.color(255, 180, 120, 60),  // Gold
            p.color(120, 255, 200, 60)   // Cyan
        ]);
        this.life = p.random(200, 400);
    }

    reset(width: number, height: number): void {
        this.x = this.p.random(width);
        this.y = height + 10;
        this.vx = this.p.random(-0.3, 0.3);
        this.vy = this.p.random(-0.5, -0.1);
        this.size = this.p.random(2, 6);
        this.life = this.p.random(200, 400);
        this.age = 0;
    }

    update(width: number, height: number): void {
        this.x += this.vx + this.p.sin(this.age * 0.02) * 0.3;
        this.y += this.vy;
        this.age++;

        if (this.age > this.life || this.y < -20) {
            this.reset(width, height);
        }
    }

    draw(p: p5): void {
        const fadeAlpha = p.map(this.age, 0, this.life, 1, 0);
        const c = this.hue;
        p.noStroke();
        p.fill(p.red(c), p.green(c), p.blue(c), p.alpha(c) * fadeAlpha);
        p.ellipse(this.x, this.y, this.size * 2);
        p.fill(p.red(c), p.green(c), p.blue(c), p.alpha(c) * fadeAlpha * 2);
        p.ellipse(this.x, this.y, this.size);
    }
}

/**
 * Click effect ripple
 */
export class ClickEffect {
    x: number;
    y: number;
    age: number = 0;

    constructor(x: number, y: number) {
        this.x = x;
        this.y = y;
    }

    draw(p: p5): void {
        this.age++;
        const alpha = p.max(0, 255 - this.age * 15);
        const size = this.age * 3;
        p.noFill();
        p.stroke(120, 200, 255, alpha);
        p.strokeWeight(2);
        p.ellipse(this.x, this.y, size);
    }

    isAlive(): boolean {
        return this.age < 20;
    }
}

/**
 * Ring click effect (for arcane rings puzzle)
 */
export class RingClickEffect {
    radius: number;
    age: number = 0;

    constructor(radius: number) {
        this.radius = radius;
    }

    draw(p: p5, cx: number, cy: number): void {
        this.age++;
        const alpha = p.max(0, 255 - this.age * 10);
        p.noFill();
        p.stroke(180, 150, 100, alpha);
        p.strokeWeight(2);
        p.ellipse(cx, cy, this.radius * 2 + this.age * 2);
    }

    isAlive(): boolean {
        return this.age < 25;
    }
}
