# Never Ending Story

A text-based web game where users explore rooms in a 2D grid world.

## Features

- **User Authentication**: Register and login with email and password
- **Room Creation**: Each new user gets a room at position [x+1, y+1]
- **Movement**: Navigate between rooms using directional commands (up, down, left, right)
- **Room Discovery**: View all existing rooms in the game world

## Tech Stack

- **Frontend**: React
- **Backend**: PHP
- **Database**: MySQL

## Prerequisites

- Node.js (v14 or higher)
- PHP (v7.4 or higher)
- MySQL (v5.7 or higher)
- npm

## Setup Instructions

### 1. Clone the Repository

```bash
git clone https://github.com/richardlipka1/neverendingstory.git
cd neverendingstory
```

### 2. Database Setup

```bash
# Login to MySQL
mysql -u root -p

# Create the database and tables
mysql -u root -p < database/schema.sql
```

### 3. Backend Configuration

```bash
# Copy the example database config
cp backend/config/database.example.php backend/config/database.php

# Edit the database configuration with your credentials
# Update the values in backend/config/database.php:
# - host: your MySQL host (default: localhost)
# - database: neverendingstory
# - username: your MySQL username
# - password: your MySQL password
```

### 4. Start PHP Backend Server

```bash
# Start PHP built-in server on port 8000
cd backend
php -S localhost:8000
```

Keep this terminal running.

### 5. Install and Start React Frontend

Open a new terminal:

```bash
cd frontend
npm install
npm start
```

The React app will start on `http://localhost:3000`.

## How to Play

1. **Register**: Create a new account with your email and password
2. **Login**: Sign in with your credentials
3. **Explore**: Use the arrow buttons to move up, down, left, or right
4. **Discover**: Click "Show All Rooms" to see all rooms in the game world
5. **Navigate**: Each user starts at their own room position and can move freely

## Game Mechanics

- Each new user is assigned a unique room at coordinates (x+1, y+1) from the previous user
- First user starts at position (0, 0)
- Movement creates new rooms if they don't exist at the target position
- All users can see all rooms and their owners
- Current position is highlighted when viewing all rooms

## Project Structure

```
neverendingstory/
├── backend/
│   ├── api/
│   │   ├── register.php      # User registration
│   │   ├── login.php          # User login
│   │   ├── logout.php         # User logout
│   │   ├── position.php       # Get current position
│   │   ├── move.php           # Move to new position
│   │   ├── rooms.php          # List all rooms
│   │   └── status.php         # Check auth status
│   ├── config/
│   │   └── database.example.php
│   └── Database.php           # Database connection helper
├── database/
│   └── schema.sql             # Database schema
├── frontend/
│   ├── public/
│   ├── src/
│   │   ├── components/
│   │   │   ├── Auth.js        # Login/Register component
│   │   │   └── Game.js        # Game interface component
│   │   ├── App.js
│   │   └── App.css
│   └── package.json
└── README.md
```

## API Endpoints

- `POST /api/register.php` - Register a new user
- `POST /api/login.php` - Login user
- `GET /api/logout.php` - Logout user
- `GET /api/position.php` - Get current position
- `POST /api/move.php` - Move in a direction (body: `{"direction": "up|down|left|right"}`)
- `GET /api/rooms.php` - Get all rooms
- `GET /api/status.php` - Check authentication status

## Development

### Backend
The backend uses PHP with PDO for database access. CORS is configured to allow requests from `http://localhost:3000`.

### Frontend
The frontend is built with React and uses the Fetch API for HTTP requests. Session cookies are used for authentication.

## Troubleshooting

**Database Connection Error**: Make sure MySQL is running and the credentials in `backend/config/database.php` are correct.

**CORS Error**: Ensure the PHP backend is running on port 8000 and React is on port 3000.

**Port Already in Use**: Change the port in the PHP command or kill the process using the port.

## License

MIT

