// tappersia/assets/js/admin/app.js
import { initializeApp } from './app-logic/main.js';

if (window.yab_data) {
    initializeApp(window.yab_data);
} else {
    console.error("YAB Data object not found.");
    // Handle the error appropriately, maybe show a message to the user.
}