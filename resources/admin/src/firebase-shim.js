// Lightweight shim for Firebase APIs used by the admin UI.
// Provides no-op behavior by default and can load the Firebase
// compat SDK from CDN when VITE_FIREBASE_ENABLE === 'true'.

const firebaseEnabled = import.meta.env.VITE_FIREBASE_ENABLE === 'true';
let _real = null;
let _loaderPromise = null;

function loadScript(src) {
  return new Promise((resolve, reject) => {
    if (document.querySelector(`script[src="${src}"]`)) return resolve();
    const s = document.createElement('script');
    s.src = src;
    s.onload = () => resolve();
    s.onerror = (e) => reject(e);
    document.head.appendChild(s);
  });
}

export function prepareFirebaseCDN() {
  if (!firebaseEnabled) return Promise.resolve(false);
  if (_loaderPromise) return _loaderPromise;
  _loaderPromise = (async () => {
    const ver = import.meta.env.VITE_FIREBASE_CDN_VERSION || '9.22.0';
    const base = `https://www.gstatic.com/firebasejs/${ver}`;
    try {
      await loadScript(`${base}/firebase-app-compat.js`);
      await loadScript(`${base}/firebase-auth-compat.js`);
      await loadScript(`${base}/firebase-firestore-compat.js`);
      _real = window.firebase || null;
      return !!_real;
    } catch (e) {
      console.error('Failed to load Firebase CDN scripts', e);
      _real = null;
      return false;
    }
  })();
  return _loaderPromise;
}

// initializeApp returns either the real app (if loaded) or a shim object
export function initializeApp(config) {
  if (firebaseEnabled && _real) {
    try {
      return _real.initializeApp(config);
    } catch (e) {
      return _real.app();
    }
  }
  return { __shim: true, config };
}

export function getAuth(app) {
  if (firebaseEnabled && _real) return _real.auth();
  return {
    currentUser: null,
    onAuthStateChanged: (cb) => { cb(null); return () => {}; },
    signOut: async () => {},
  };
}

export const signInWithCustomToken = async () => { throw new Error('Firebase disabled or not loaded'); };
export const signInAnonymously = async () => { throw new Error('Firebase disabled or not loaded'); };
export const onAuthStateChanged = (auth, cb) => { cb(null); return () => {}; };
export const signOut = async () => {};

export function getFirestore(app) { if (firebaseEnabled && _real) return _real.firestore(); return { __shim_db: true }; }

export function collection(db, ...path) { if (firebaseEnabled && _real) return _real.firestore().collection(path.join('/')); return { __coll: path }; }
export function doc(db, ...path) { if (firebaseEnabled && _real) return _real.firestore().doc(path.join('/')); return { __doc: path }; }
export async function addDoc(colRef, data) { if (firebaseEnabled && _real) return await colRef.add(data); return { id: Math.random().toString(36).slice(2,9) }; }
export async function updateDoc(docRef, data) { if (firebaseEnabled && _real) return await docRef.update(data); return; }
export function onSnapshot(ref, cb, errCb) { if (firebaseEnabled && _real) return ref.onSnapshot(cb, errCb); try { cb({ docs: [] }); } catch (e) { errCb?.(e); } return () => {}; }
export function serverTimestamp() { if (firebaseEnabled && _real && _real.firestore && _real.firestore.FieldValue) return _real.firestore.FieldValue.serverTimestamp(); return new Date(); }
export function query(...args) { return args; }
export function where(...args) { return args; }
export function orderBy(...args) { return args; }
export function increment(n) { if (firebaseEnabled && _real && _real.firestore && _real.firestore.FieldValue) return _real.firestore.FieldValue.increment(n); return { __inc: n }; }
export async function deleteDoc(docRef) { if (firebaseEnabled && _real) return await docRef.delete(); return; }
export async function setDoc(docRef, data, opts) { if (firebaseEnabled && _real) return await docRef.set(data, opts); return; }
export async function getDoc(docRef) { if (firebaseEnabled && _real) { const snap = await docRef.get(); return { exists: () => snap.exists, data: () => snap.data() }; } return { exists: () => false, data: () => null }; }

export default {
  prepareFirebaseCDN,
  initializeApp,
  getAuth,
  getFirestore,
  collection,
  doc,
  addDoc,
  updateDoc,
  onSnapshot,
  serverTimestamp,
  query,
  where,
  orderBy,
  increment,
  deleteDoc,
  setDoc,
  getDoc,
};
