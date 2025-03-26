const isDevelopment = process.env.NODE_ENV === 'development';

export const config = {
    wsUrl: 'ws://localhost:2567',
    apiUrl: isDevelopment ? 'https://gamdle-royale.test' : 'https://gamdle-royale.fr',
    roomUrl: isDevelopment ? 'https://gamdle-royale.test' : 'https://gamdle-royale.fr'
}; 