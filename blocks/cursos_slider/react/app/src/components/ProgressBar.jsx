import React from 'react';

const ProgressBar = ({ progress }) => {
  return (
    <div className="progress-circle" data-progress={progress}>
      <svg viewBox="0 0 50 50">
        <circle 
          cx="25" 
          cy="25" 
          r="20" 
          stroke="#e6e6e6" 
          strokeWidth="4"
        />
        <circle 
          className="progress" 
          cx="25" 
          cy="25" 
          r="20" 
          fill="none"
          strokeWidth="4"
          strokeDasharray="125.66 125.66"
          strokeDashoffset={125.66 - (125.66 * (progress || 0) / 100)}
          stroke={
            progress === 100 ? '#28a745' : 
            progress === 0 ? '#f1f1f1' : 
            'var(--primary)'
          }
        />
      </svg>
      <div className="progress-text">
        <span className="normal-text">{progress}%</span>
      </div>
    </div>
  );
};

export default ProgressBar; 