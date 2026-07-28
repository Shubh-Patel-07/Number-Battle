export const validNumber = value => typeof value === 'string' && /^\d{4}$/.test(value) && new Set(value).size === 4;
export function score(secret, guess) { const correctPositions = [...guess].filter((d, i) => secret[i] === d).length; const correctDigits = [...guess].filter(d => secret.includes(d)).length; return { correctDigits, correctPositions, wrongDigits: 4 - correctDigits }; }
