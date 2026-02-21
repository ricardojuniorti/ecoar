import axios from 'axios';

const api = axios.create({
    // O Vite detecta sozinho se você está rodando 'npm run dev' ou 'npm run build'
    baseURL: import.meta.env.VITE_API_URL, 
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
});

export default api;