import {
  getEchoInstance,
  StrictPresenceChannel,
} from '@/datasources/websocket/echo';

type SecondPlayerReadyEvent = {
  type: 'SecondPlayerReady';
  payload: never;
};

type SecondPlayerUnreadyEvent = {
  type: 'SecondPlayerUnready';
  payload: never;
};

type NewGameStartedEvent = {
  type: 'NewGameStarted';
  payload: {
    room: {
      ulid: string;
    };
    roomGame: {
      ulid: string;
      games: number[][];
    };
  };
};

type NextTurnAvailableEvent = {
  type: 'NextTurnAvailable';
  payload: {
    room: {
      ulid: string;
    };
    roomGame: {
      ulid: string;
      games: number[][];
    };
    user: {
      ulid: string;
    };
  };
};

type GameFinishedEvent = {
  type: 'GameFinished';
  payload: {
    room: {
      ulid: string;
    };
    roomGame: {
      ulid: string;
      games: number[][];
    };
    winner: {
      ulid: string;
    };
  };
};

type RoomGameEvent =
  | SecondPlayerReadyEvent
  | SecondPlayerUnreadyEvent
  | NewGameStartedEvent
  | NextTurnAvailableEvent
  | GameFinishedEvent;

export interface RoomGameChannel extends StrictPresenceChannel<RoomGameEvent> {}

export const getRoomChannel = (channelId: string): RoomGameChannel => {
  return getEchoInstance().join(channelId) as RoomGameChannel;
};
