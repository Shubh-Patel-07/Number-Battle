import test from 'node:test';
import assert from 'node:assert/strict';
import { score, validNumber } from './game.js';

test('only four distinct digits are valid secrets and guesses', () => {
  assert.equal(validNumber('4829'), true);
  assert.equal(validNumber('4889'), false);
  assert.equal(validNumber('123'), false);
  assert.equal(validNumber('12ab'), false);
});

test('score separates exact positions from matching digits', () => {
  assert.deepEqual(score('4831', '4138'), { correctDigits: 4, correctPositions: 2, wrongDigits: 0 });
  assert.deepEqual(score('4829', '4829'), { correctDigits: 4, correctPositions: 4, wrongDigits: 0 });
});
