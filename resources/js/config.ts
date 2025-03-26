const isDevelopment = process.env.NODE_ENV === 'development';

export const config = {
    wsUrl: isDevelopment ? 'ws://localhost:2567' : 'wss://server.gamdle-royale.fr',
    apiUrl: isDevelopment ? 'https://gamdle-royale.test' : 'https://gamdle-royale.fr',
    roomUrl: isDevelopment ? 'https://gamdle-royale.test' : 'https://gamdle-royale.fr'
}; 
