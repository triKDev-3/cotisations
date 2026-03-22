import './bootstrap';

import Alpine from 'alpinejs';
import * as firebase from './firebase';

window.Alpine = Alpine;
window.firebase = firebase;
window.db = firebase.db;
window.auth = firebase.auth;

Alpine.start();
