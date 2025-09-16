export class SlideShow extends HTMLDivElement {


    private pager: HTMLElement;
    constructor(
    ) {
        super();
        let pager: HTMLElement|null = this.querySelector('nav.slideshow-pager');

        if (pager === null) {
            throw new Error('Slideshows require a pager');
        }

        this.pager = pager;
    }

    connectedCallback(): void {
        this.pager.querySelectorAll('a').forEach((a: HTMLAnchorElement) => {
            a.addEventListener('click', (e: MouseEvent) => {
                a.classList.add('active');
                e.preventDefault();
                let targetSlide = this.querySelector(`${a.getAttribute('href')}`);
                targetSlide?.scrollIntoView(
                    {
                        behavior: "smooth"
                    }
                );
            })
        })
    }
}
