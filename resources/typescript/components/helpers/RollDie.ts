export default function rollDie(dieSides: number): number {
    return Math.floor(Math.random() * dieSides) + 1;
}
