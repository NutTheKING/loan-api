// Minimal stub to satisfy imports for the 'firebase' package during build
export function initializeApp() { return { __empty: true }; }
export function getAuth() { return { currentUser: null, onAuthStateChanged: (cb) => { cb(null); return () => {}; }, signOut: async () => {} }; }
export function getFirestore() { return { __empty_db: true }; }
export const signInWithCustomToken = async () => { throw new Error('Firebase disabled in build'); };
export const signInAnonymously = async () => { throw new Error('Firebase disabled in build'); };
export const onAuthStateChanged = (auth, cb) => { cb(null); return () => {}; };
export const signOut = async () => {};
export function collection() { return { __coll: true }; }
export function doc() { return { __doc: true }; }
export async function addDoc() { return { id: Math.random().toString(36).slice(2,9) }; }
export async function updateDoc() { return; }
export function onSnapshot(ref, cb, errCb) { try { cb({ docs: [] }); } catch (e) { errCb?.(e); } return () => {}; }
export function serverTimestamp() { return new Date(); }
export function query(...args) { return args; }
export function where(...args) { return args; }
export function orderBy(...args) { return args; }
export function increment(n) { return { __inc: n }; }
export async function deleteDoc() { return; }
export async function setDoc() { return; }
export async function getDoc() { return { exists: () => false, data: () => null }; }

export default {};
