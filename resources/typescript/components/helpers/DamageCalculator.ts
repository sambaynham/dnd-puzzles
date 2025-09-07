import rollDie from "./RollDie";

export default function calculateDamage(dieSides: number, dieCount: number): number {

    let rolls: number[] = [];
    for(let i: number = 0; i < dieCount; i++) {
        let rollDamage = rollDie(dieSides);
        rolls.push(rollDamage);
    }

    let total = 0;
    rolls.forEach(roll => {
        total += roll;
    });
    return total;
}
