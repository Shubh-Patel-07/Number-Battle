# Number Battle

Real-time, two-player number-guessing game. Players choose a secret four-digit number (unique digits), alternate guesses, and receive exact-position and digit-only feedback.

## Quick start

1. Copy `.env.example` to `server/.env` and set `MONGODB_URI` and a long `JWT_SECRET`.
2. Run `npm install`.
3. Run `npm run dev`, then open `http://localhost:5173`.

The REST API is served at `/api`; Socket.IO powers room presence, game actions, chat, WebRTC signalling and reconnection. The server is authoritative: secrets stay server-side and all guesses/turns are validated there.

## Main endpoints

`POST /api/auth/register`, `POST /api/auth/login`, `GET /api/leaderboard`, `POST /api/rooms`, `GET /api/rooms/:code`.

## Deployment

Set production environment variables, build the client (`npm run build`), serve `client/dist` behind HTTPS, and configure a TURN server for reliable WebRTC across restrictive networks. `docker compose up --build` starts MongoDB and the API.
