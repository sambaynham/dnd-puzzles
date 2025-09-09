export default class MessageOfTheDay extends HTMLSpanElement {
    static messages: string[] = [
        'Rollin\' with the Gnolls',
        'Compatible with yo mama',
        'Are you sure you want to do that?',
        'Well, you may certainly try',
        'Where fun goes to overthink',
        'Ask your DM if DND Puzzles is right for you',
        'May cause Lycanthropy',
        'Not for external use',
        'Frustration builds character'
    ];

    connectedCallback(): void {
        const randomIndex = Math.floor(Math.random() * MessageOfTheDay.messages.length);
        this.innerText = MessageOfTheDay.messages[randomIndex];
    }
}
