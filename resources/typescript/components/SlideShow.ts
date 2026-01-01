export class SlideShow extends HTMLDivElement {

    private pager: HTMLElement;

    private slides: NodeListOf<HTMLLIElement>;

    constructor(
    ) {
        super();
        let pager: HTMLElement|null = this.querySelector('nav.slideshow-pager');
        let slides: NodeListOf<HTMLLIElement> = this.querySelectorAll('.slideshow-slide');


        if (pager === null) {
            throw new Error('Slideshows require a pager');
        }

        if (slides.length === 0) {
            throw new Error('Slideshows require slides.');
        }

        this.slides = slides;
        this.pager = pager;
    }

    connectedCallback(): void {
        this.pager.querySelectorAll('a').forEach((a: HTMLAnchorElement) => {
            a.addEventListener('click', (e: MouseEvent) => {
                e.preventDefault();
                this.advanceTo(`${a.getAttribute('href')}`);
            });
        });
    }


    private advanceTo(targetSelector: string): void {
        let targetSlide: HTMLLIElement|null = this.querySelector(targetSelector);
        if (targetSlide === null) {
            throw new Error('Target slide not found');
        }
        this.slides.forEach((slide: HTMLLIElement) => {
            slide.classList.remove('active');
        });
        targetSlide.classList.add('active');

        this.pager.querySelectorAll('a').forEach((a: HTMLAnchorElement) => {
            a.classList.remove('active');
            if (a.getAttribute('href') === targetSelector) {
                a.classList.add('active');
            }
        });
    }
}
