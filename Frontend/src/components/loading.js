import React from "react";
// inspired by https://codepen.io/nikhil8krishnan/pen/rVoXJa

export default class LoadingScreen extends React.Component {
  render() {
    return (
      <svg
        version="1.1"
        id="L5"
        x="0px"
        y="0px"
        viewBox="0 0 100 100"
        style={{
            width: '100px',
            height: '100px',
            margin: '20px',
            display:'inline-block',
            position: 'fixed',
            top: '40%',
            left: 'calc(50% - 80px)',
        }}
      >
        <circle fill="#1b4a6b" stroke="none" cx="6" cy="50" r="6">
          <animateTransform
            attributeName="transform"
            dur="1s"
            type="translate"
            values="0 15 ; 0 -15; 0 15"
            repeatCount="indefinite"
            begin="0.1"
          />
        </circle>
        <circle fill="#1b4a6b" stroke="none" cx="30" cy="50" r="6">
          <animateTransform
            attributeName="transform"
            dur="1s"
            type="translate"
            values="0 10 ; 0 -10; 0 10"
            repeatCount="indefinite"
            begin="0.2"
          />
        </circle>
        <circle fill="#1b4a6b" stroke="none" cx="54" cy="50" r="6">
          <animateTransform
            attributeName="transform"
            dur="1s"
            type="translate"
            values="0 5 ; 0 -5; 0 5"
            repeatCount="indefinite"
            begin="0.3"
          />
        </circle>
      </svg>
    );
  }
}
