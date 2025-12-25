import React, { useState, useEffect } from 'react';

const API_BASE = 'http://localhost:8000/api';

function Game({ user, onLogout }) {
  const [position, setPosition] = useState({ x: 0, y: 0 });
  const [rooms, setRooms] = useState([]);
  const [message, setMessage] = useState('');
  const [loading, setLoading] = useState(false);
  const [showRooms, setShowRooms] = useState(false);

  useEffect(() => {
    loadPosition();
  }, []);

  const loadPosition = async () => {
    try {
      const response = await fetch(`${API_BASE}/position.php`, {
        credentials: 'include',
      });
      const data = await response.json();
      if (response.ok) {
        setPosition(data.position);
      }
    } catch (error) {
      console.error('Error loading position:', error);
    }
  };

  const handleMove = async (direction) => {
    setLoading(true);
    setMessage('');
    try {
      const response = await fetch(`${API_BASE}/move.php`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        credentials: 'include',
        body: JSON.stringify({ direction }),
      });

      const data = await response.json();

      if (response.ok) {
        setPosition(data.position);
        setMessage(data.message);
        if (showRooms) {
          loadRooms();
        }
      } else {
        setMessage(data.error || 'Failed to move');
      }
    } catch (error) {
      setMessage('Network error. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  const loadRooms = async () => {
    try {
      const response = await fetch(`${API_BASE}/rooms.php`, {
        credentials: 'include',
      });
      const data = await response.json();
      if (response.ok) {
        setRooms(data.rooms);
        setShowRooms(true);
      }
    } catch (error) {
      console.error('Error loading rooms:', error);
    }
  };

  return (
    <div className="game-container">
      <div className="game-header">
        <h1>Never Ending Story</h1>
        <div className="user-info">
          <div>{user.email}</div>
          <button className="btn" onClick={onLogout} style={{ marginTop: '10px' }}>
            Logout
          </button>
        </div>
      </div>

      <div className="position-display">
        <h2>Current Position</h2>
        <div className="position-coords">
          X: {position.x}, Y: {position.y}
        </div>
      </div>

      <div className="controls">
        <h3>Move Commands</h3>
        <div className="direction-buttons">
          <button
            className="btn"
            onClick={() => handleMove('up')}
            disabled={loading}
          >
            ↑ Up
          </button>
          <button
            className="btn"
            onClick={() => handleMove('left')}
            disabled={loading}
          >
            ← Left
          </button>
          <button
            className="btn"
            onClick={() => handleMove('right')}
            disabled={loading}
          >
            → Right
          </button>
          <button
            className="btn"
            onClick={() => handleMove('down')}
            disabled={loading}
          >
            ↓ Down
          </button>
        </div>
      </div>

      {message && <div className="message">{message}</div>}

      <div className="rooms-section">
        <h3>
          Rooms
          <button
            className="btn"
            onClick={loadRooms}
            style={{ marginLeft: '15px', padding: '8px 15px' }}
          >
            Show All Rooms
          </button>
        </h3>
        {showRooms && (
          <div className="rooms-list">
            {rooms.length === 0 ? (
              <div>No rooms found</div>
            ) : (
              rooms.map((room, index) => (
                <div
                  key={index}
                  className={`room-item ${room.isCurrent ? 'current' : ''}`}
                >
                  <div className="room-coords">
                    Position ({room.x}, {room.y})
                  </div>
                  <div className="room-owner">
                    Owner: {room.owner}
                  </div>
                </div>
              ))
            )}
          </div>
        )}
      </div>
    </div>
  );
}

export default Game;
