export class SlideShow extends HTMLUListElement{

    constructor(
        private items: NodeListOf<HTMLLIElement>,
        private slideShowPager: HTMLUListElement
    ) {
        super();
        this.items = this.querySelectorAll('li');
        this.slideShowPager = document.createElement('ul');
        this.slideShowPager.classList.add('slideshow-pager');
    }
    connectedCallback(): void {




        this.items.forEach((item: HTMLLIElement) => {
            let pagerItem:HTMLLIElement = document.createElement('li');
            if (item.dataset.delta === undefined) {
                throw new Error('Delta must be defined');
            }
            pagerItem.dataset.delta = item.dataset.delta;
            pagerItem.innerHTML = item.dataset.delta;

            this.slideShowPager.appendChild(pagerItem);
        });

        this.appendChild(this.slideShowPager);

        this.arrangeItems();
    }

    private arrangeItems(): void {

    }
}
