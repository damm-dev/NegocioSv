import './bootstrap'; 

import React from 'react';
import ReactDOM from 'react-dom/client';
import Example from './components/Example';
import HeroSection from './components/index';

// Renderiza componente React en el elemento con el ID 'app'
const rootElement = document.getElementById('app');

if (rootElement) {
    ReactDOM.createRoot(rootElement).render(
        <React.StrictMode>
            <HeroSection />
        </React.StrictMode>
    );
}
