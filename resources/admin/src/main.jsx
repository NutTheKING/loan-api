import React from 'react';
import ReactDOM from 'react-dom/client';
import './index.css';
import App from './App';
import { prepareFirebaseCDN } from './firebase-shim';

const root = ReactDOM.createRoot(document.getElementById('root'));

(async () => {
  try {
    await prepareFirebaseCDN();
  } catch (e) {
    console.warn('Firebase CDN prepare failed:', e);
  }
  root.render(
    <React.StrictMode>
      <App />
    </React.StrictMode>
  );
})();