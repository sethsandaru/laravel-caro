import Echo, { PresenceChannel } from 'laravel-echo';

let echoInstance: Echo | undefined;

export const getEchoInstance = (): Echo => {
  if (echoInstance) {
    return echoInstance;
  }

  echoInstance = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
  });

  return echoInstance;
};

type BaseEvent = {
  type: string;
  payload: unknown;
};

export interface StrictPresenceChannel<Event extends BaseEvent>
  extends PresenceChannel {
  listen<EventType extends Event['type']>(
    event: EventType,
    callback: (data: Extract<Event, { type: EventType }>['payload']) => void
  ): this;
}
