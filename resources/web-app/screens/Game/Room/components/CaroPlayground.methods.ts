export const getDefaultBoard = (): number[][] => {
  const board: number[][] = [];

  for (let i = 0; i < 20; i++) {
    board[i] = [];
    for (let j = 0; j < 20; j++) {
      board[i][j] = 0;
    }
  }

  return board;
};
