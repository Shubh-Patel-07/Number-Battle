# Number Battle

Number Battle is a two-player strategy game. Each player chooses four unique secret digits, takes turns guessing the opponent's code, and gets feedback for matching digits and exact positions.

## Features

- Signup and login
- Create or join a private room using a six-character code
- Server-validated secrets, guesses, turns, wins and losses
- Live room status, guess history and chat
- Responsive dark gaming interface
- Two deployment versions:
  - **Node.js + Socket.IO** for local development or a real-time hosting service
  - **PHP + MySQL polling** for InfinityFree shared hosting

## Play locally

Requirements: Node.js 24+, npm, and MongoDB running locally.

```powershell
cd "D:\Number Battle"
Copy-Item .env.example server\.env
npm install
npm run dev
```

Open `http://localhost:5173`. You can also double-click **Number Battle** on the desktop to start the local launcher.

## InfinityFree deployment

InfinityFree cannot run Node.js, Socket.IO or WebSockets. The `infinityfree` folder is a PHP/MySQL implementation that works on InfinityFree through a two-second browser polling interval.

1. Create an InfinityFree website and a MySQL database.
2. Open the database's phpMyAdmin and import `infinityfree/database.sql`.
3. Edit `infinityfree/config.php` using the DB host, name, user and password shown in the InfinityFree control panel.
4. Upload the contents of the `infinityfree` folder to the site's `htdocs` directory.
5. Open the website URL and use two separate browsers/devices to create accounts and play.

Never upload a `config.php` containing real credentials to a public GitHub repository.

## Voice calling

The local Node.js architecture includes WebRTC signalling hooks. The InfinityFree version does **not** currently include voice calls. A basic peer-to-peer WebRTC voice feature can be added using PHP/MySQL signalling, but reliable calls across all networks require a TURN server.

## Game scoring

For every guess, the game returns:

- **Correct digits**: number of digits present in the secret
- **Correct positions**: number of digits in the same index as the secret
- **Wrong digits**: digits not present in the secret

Example: secret `4831`, guess `4138` gives 4 correct digits and 2 correct positions.

## Repository layout

```text
client/        React + Vite interface
server/        Express, Socket.IO, MongoDB backend
infinityfree/  PHP + MySQL deployment for InfinityFree
docker-compose.yml
```
