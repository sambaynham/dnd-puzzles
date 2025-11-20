export type PotionAdjustedDetail = {
    ingredientIndex: number;
    newAmount: number;
    currentMix: number[]; // RGB normalized 0-1
}

export type PotionsSolvedDetail = {
    finalMix: number[];
    targetMix: number[];
    accuracy: number;
}
