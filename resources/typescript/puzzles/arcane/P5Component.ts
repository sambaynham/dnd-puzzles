import p5 from 'p5';

/**
 * Base class for p5.js-based Web Components.
 * Manages p5 instance lifecycle and provides hooks for setup/draw.
 * Compatible with p5.js 2.0
 */
export abstract class P5Component extends HTMLDivElement {
    protected p5Instance: p5 | null = null;
    protected canvasWidth: number = 0;
    protected canvasHeight: number = 0;

    connectedCallback(): void {
        this.initP5();
    }

    disconnectedCallback(): void {
        this.destroyP5();
    }

    protected initP5(): void {
        this.p5Instance = new p5((p: p5) => {
            // p5.js 2.0 supports async setup
            p.setup = async () => {
                const bounds = this.getBoundingClientRect();
                this.canvasWidth = bounds.width;
                this.canvasHeight = bounds.height;
                await this.setup(p);
            };

            p.draw = () => this.draw(p);

            p.mousePressed = (event: MouseEvent) => {
                // Only handle if click is within this component
                if (this.isEventInBounds(event)) {
                    this.onMousePressed(p, event);
                }
            };

            p.mouseReleased = (event: MouseEvent) => {
                if (this.isEventInBounds(event)) {
                    this.onMouseReleased(p, event);
                }
            };

            p.windowResized = () => {
                this.onResize(p);
            };
        }, this);
    }

    protected destroyP5(): void {
        if (this.p5Instance) {
            this.p5Instance.remove();
            this.p5Instance = null;
        }
    }

    protected isEventInBounds(event: MouseEvent): boolean {
        const bounds = this.getBoundingClientRect();
        return (
            event.clientX >= bounds.left &&
            event.clientX <= bounds.right &&
            event.clientY >= bounds.top &&
            event.clientY <= bounds.bottom
        );
    }

    /**
     * Get mouse position relative to the component
     */
    protected getRelativeMousePos(p: p5): { x: number; y: number } {
        return {
            x: p.mouseX,
            y: p.mouseY
        };
    }

    /**
     * Called once when the p5 instance is created.
     * Override to set up canvas and initial state.
     * Can be async for loading resources.
     */
    protected abstract setup(p: p5): void | Promise<void>;

    /**
     * Called every frame.
     * Override to render the puzzle.
     */
    protected abstract draw(p: p5): void;

    /**
     * Called when mouse is pressed within the component.
     */
    protected onMousePressed(_p: p5, _event: MouseEvent): void {}

    /**
     * Called when mouse is released within the component.
     */
    protected onMouseReleased(_p: p5, _event: MouseEvent): void {}

    /**
     * Called when window is resized.
     * Override to handle canvas resizing.
     */
    protected onResize(p: p5): void {
        const rect = this.getBoundingClientRect();
        this.canvasWidth = rect.width;
        this.canvasHeight = rect.height;
        p.resizeCanvas(this.canvasWidth, this.canvasHeight);
    }

    /**
     * Public method to reset the puzzle.
     * Override in subclasses.
     */
    public reset(): void {}

    /**
     * Public method to give a hint.
     * Override in subclasses.
     */
    public giveHint(): void {}
}
