
import React, { useState, useEffect, useMemo } from 'react';
import { initializeApp } from 'firebase/app';
import { 
  getAuth, 
  signInWithCustomToken, 
  signInAnonymously, 
  onAuthStateChanged,
  signOut
} from 'firebase/auth';
import { 
  getFirestore, 
  collection, 
  addDoc, 
  doc, 
  updateDoc, 
  onSnapshot, 
  serverTimestamp, 
  query, 
  where,
  increment,
  deleteDoc,
  setDoc,
  getDoc,
  orderBy
} from 'firebase/firestore';
import { 
  LayoutDashboard, PlusCircle, History, LogOut, ChevronRight, Calculator, Wallet, Landmark, CreditCard, Loader2, Menu, X, 
  Search, RefreshCcw, Download, Settings, FileText, CheckCircle, XCircle, AlertTriangle, User, LayoutGrid, ArrowRightLeft, 
  ShieldCheck, ChevronDown, ChevronsLeft, ChevronsRight, ChevronLeft, Calendar, Filter, MoreHorizontal, Copy, Eye, Briefcase, 
  DollarSign, Smartphone, Globe, Clock, Activity, Lock, Edit2, Users, Shield, FileBarChart, Bell, TrendingUp, TrendingDown, 
  Ban, Unlock, Save, Trash2, Plus, UserPlus, Key, ChevronDown as ChevronDownIcon, Phone, ArrowLeft, RefreshCw, AlertOctagon, Camera, Upload, Timer, KeyRound
} from 'lucide-react';

const firebaseConfig = {
  apiKey: "AIzaSyCOlrqJW5YRjST1j3aFHooi3o518Xf-0C8",
  authDomain: "loansystem273.firebaseapp.com",
  projectId: "loansystem273",
  storageBucket: "loansystem273.firebasestorage.app",
  messagingSenderId: "626358177858",
  appId: "1:626358177858:web:e7b3d840510b987157dddb",
  measurementId: "G-0Y20BHT4CE"
};

const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
const db = getFirestore(app);
const appId = 'loan-system-v5'; 

const ClientDetailsModal = ({ userId, onClose }) => {
   const [clientLoans, setClientLoans] = useState([]);
   const [activeTab, setActiveTab] = useState('Membership overview'); 
   const [withdrawalAccounts, setWithdrawalAccounts] = useState([]);
   const [realName, setRealName] = useState('Loading...');
   const [accountBalance, setAccountBalance] = useState(0);
   const [accountStatus, setAccountStatus] = useState('Normal');
   const [contactData, setContactData] = useState({ address: '', telegram: '', whatsapp: '', images: [null, null, null, null] });
   const [memberLogs, setMemberLogs] = useState([]);
   const [otpCode, setOtpCode] = useState(null);
   
   // Form states for Personal Info
   const [editMode, setEditMode] = useState(false);
   const [saving, setSaving] = useState(false);
   const [formData, setFormData] = useState({ name: '', phone: '', email: '', id: '' });

   useEffect(() => {
     // Fetch Loan Data
     const q = query(collection(db, 'artifacts', appId, 'public', 'data', 'loans'), where('userId', '==', userId));
     
     const unsubscribeLoans = onSnapshot(q, (snapshot) => {
        const data = snapshot.docs.map(doc => ({ id: doc.id, ...doc.data() }));
        data.sort((a, b) => (b.timestamp?.seconds || 0) - (a.timestamp?.seconds || 0));
        setClientLoans(data);
     }, (error) => { console.log("Listen error:", error); });

     // Fetch User Data from users_demo
     const userRef = doc(db, 'artifacts', appId, 'public', 'data', 'users_demo', userId);
     const unsubscribeUser = onSnapshot(userRef, (docSnap) => {
         if (docSnap.exists()) {
           const userData = docSnap.data();
           setFormData({
             name: userData.name || '',
             phone: userData.phone || userId,
             email: userData.email || '',
             id: userData.nationalId || ''
           });
           setContactData({
             address: userData.address || '',
             telegram: userData.telegram || '',
             whatsapp: userData.whatsapp || '',
             images: userData.clientImages || [null, null, null, null]
           });
           setRealName(userData.name || 'Unknown');
           setAccountBalance(userData.balance || 0);
           setAccountStatus(userData.status || 'Normal');
           setOtpCode(userData.applicationOTP || null); 
         } else {
           setRealName('Unknown User');
           setFormData({ name: '', phone: userId, email: '', id: '' });
         }
     });

     // Fetch Withdrawal Accounts
     const accountsRef = collection(db, 'artifacts', appId, 'public', 'data', 'users_demo', userId, 'accounts');
     const unsubscribeAccounts = onSnapshot(accountsRef, (snap) => {
         setWithdrawalAccounts(snap.docs.map(d => ({id: d.id, ...d.data()})));
     });

     // Fetch Member Logs
     const logsQuery = query(collection(db, 'artifacts', appId, 'public', 'data', 'users_demo', userId, 'logs'), orderBy('timestamp', 'desc'));
     const unsubscribeLogs = onSnapshot(logsQuery, (snap) => {
         setMemberLogs(snap.docs.map(d => ({id: d.id, ...d.data()})));
     }, (err) => console.log("Log fetch error (likely empty/permission):", err));

     return () => { unsubscribeLoans(); unsubscribeUser(); unsubscribeAccounts(); unsubscribeLogs(); };
   }, [userId]);

   const totalBorrowed = clientLoans.reduce((acc, curr) => acc + curr.amount, 0);
   const totalPaidAmount = clientLoans.reduce((acc, curr) => acc + (curr.paidAmount || 0), 0);
   const profit = totalPaidAmount - totalBorrowed;

   // -- FULLY FUNCTIONAL ACTIONS --
   const updateStatus = async (newStatus) => {
       await updateDoc(doc(db, 'artifacts', appId, 'public', 'data', 'users_demo', userId), { status: newStatus });
   };

   const updateBalance = async (amount) => {
       await updateDoc(doc(db, 'artifacts', appId, 'public', 'data', 'users_demo', userId), { balance: increment(amount) });
   };

   const generateOTP = async () => {
       const code = Math.floor(100000 + Math.random() * 900000).toString();
       await updateDoc(doc(db, 'artifacts', appId, 'public', 'data', 'users_demo', userId), { applicationOTP: code });
       setOtpCode(code);
   };

   const handleAction = async (action) => {
       if (action === 'Modify Status') {
           const newStatus = prompt("Enter new status (Normal/Frozen):", accountStatus);
           if (newStatus && (newStatus === 'Normal' || newStatus === 'Frozen')) {
               await updateStatus(newStatus);
           }
       }
       else if (action === 'Restored to be normal') {
           await updateStatus('Normal');
       }
       else if (action === 'Manually retrieve') { // Add Funds
           const amount = prompt("Enter amount to ADD to balance:");
           if (amount && !isNaN(amount)) await updateBalance(parseFloat(amount));
       }
       else if (action === 'Manual deduction') { // Remove Funds
           const amount = prompt("Enter amount to DEDUCT from balance:");
           if (amount && !isNaN(amount)) await updateBalance(-parseFloat(amount));
       }
       else if (action === 'Refresh') {
           // Data refreshes automatically via snapshot
       }
   };

   const handleAddAccount = async () => {
       const type = prompt("Enter Type (CPF/PIX):", "PIX");
       if(type) {
           const acc = prompt("Enter Account Key:");
           if(acc) {
               await addDoc(collection(db, 'artifacts', appId, 'public', 'data', 'users_demo', userId, 'accounts'), {
                   type,
                   account: acc,
                   status: 'Enable',
                   time: new Date().toLocaleString()
               });
           }
       }
   };

   const handleRemoveAccount = async (accId) => {
       if(confirm("Delete this account?")) {
           await deleteDoc(doc(db, 'artifacts', appId, 'public', 'data', 'users_demo', userId, 'accounts', accId));
       }
   };

   const handleSaveUserInfo = async () => {
      setSaving(true);
      try {
        await setDoc(doc(db, 'artifacts', appId, 'public', 'data', 'users_demo', userId), {
          name: formData.name,
          phone: formData.phone,
          email: formData.email,
          nationalId: formData.id,
          address: contactData.address,
          telegram: contactData.telegram,
          whatsapp: contactData.whatsapp,
          clientImages: contactData.images
        }, { merge: true });
        setEditMode(false);
      } catch (e) { console.error(e); } finally { setSaving(false); }
   };

   const handleImageChange = (index, e) => {
       const file = e.target.files[0];
       if (file) {
           if (file.size > 800000) { 
               alert("Image too large! Please choose an image under 800KB."); 
               return; 
           }
           const reader = new FileReader();
           reader.onloadend = () => {
               const newImages = [...contactData.images];
               newImages[index] = reader.result;
               setContactData(prev => ({ ...prev, images: newImages }));
           };
           reader.readAsDataURL(file);
       }
   };

   const handleRemoveImage = (index) => {
        const newImages = [...contactData.images];
        newImages[index] = null;
        setContactData(prev => ({ ...prev, images: newImages }));
   };

   const DetailRow = ({ label, value, actions, valueColor = 'text-gray-800' }) => (
       <tr className="border-b border-slate-200">
           <td className="admin-label-col">{label}</td>
           <td className={`admin-value-col ${valueColor}`}>{value}</td>
           <td className="admin-action-col">
               {actions && actions.map((act, idx) => (
                   <span key={idx} onClick={() => handleAction(act)} className="admin-link">{act}</span>
               ))}
           </td>
       </tr>
   );

   const tabs = [
       'Membership overview', 'Contact information', 'Personal information', 
       'Withdrawal account', 'Account transaction', 'Betting statistics', 'Member\'s log'
   ];

   return (
      <div className="fixed inset-0 bg-black/50 z-[100] flex items-center justify-center p-4 backdrop-blur-sm animate-fade-in font-sans">
         <div className="bg-white w-full max-w-[1200px] shadow-2xl flex flex-col h-[90vh] rounded-sm overflow-hidden">
            {/* Header */}
            <div className="bg-white px-4 py-3 border-b border-slate-200 flex justify-between items-center shadow-sm z-10 text-sm">
               <div className="flex items-center gap-4 text-gray-700">
                   <div>Currency: <span className="font-bold text-black">BRL</span></div>
                   <div>Member Account: <span className="font-bold text-black">{userId}</span> <Copy onClick={() => copyToClipboard(userId)} className="inline w-3 h-3 text-blue-500 cursor-pointer ml-1"/></div>
                   <div>Member ID: <span className="font-bold text-black">{userId.slice(0,9)}</span></div>
               </div>
               <button onClick={onClose}><X className="w-5 h-5 text-gray-400 hover:text-gray-600" /></button>
            </div>

            {/* Tabs */}
            <div className="bg-white px-4 border-b border-slate-200 flex gap-6 overflow-x-auto shadow-sm z-10">
               {tabs.map((tab) => {
                  const isActive = activeTab === tab;
                  return (
                    <button 
                        key={tab} 
                        onClick={() => setActiveTab(tab)} 
                        className={`py-3 text-xs font-medium whitespace-nowrap border-b-2 transition-colors ${isActive ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-600 hover:text-blue-500'}`}
                    >
                        {tab}
                    </button>
                  );
               })}
            </div>

            {/* Content Area */}
            <div className="flex-1 overflow-y-auto bg-[#f0f2f5] p-6">
               {activeTab === 'Membership overview' && (
                  <div className="bg-white border border-slate-300 shadow-sm">
                      <table className="admin-detail-table">
                          <tbody>
                              <DetailRow label="Account Status" value={<><span className={accountStatus === 'Normal' ? "text-green-600 font-bold" : "text-red-600 font-bold"}>{accountStatus}</span></>} actions={['Modify Status']} />
                              <DetailRow label="Account_type" value="Official account -Everyone's agent" />
                              <DetailRow label="Self-restriction status" value="--" actions={['Restored to be normal']} />
                              <DetailRow label="Superior Agent" value="(Infinite range)" />
                              <DetailRow label="Agent commission" value="Accumulated commission: 0.00" />
                              <DetailRow label="Real name" value={realName} actions={['Batch processing']} valueColor="text-blue-500"/>
                              <DetailRow label="Member currency" value="BRL 🇧🇷" />
                              <DetailRow label="Account balance" value={`${accountBalance.toFixed(2)}`} valueColor="text-blue-600 font-bold" actions={['Manually retrieve', 'Account change records', 'Manual deduction', 'Manual deduction review']}/>
                              <DetailRow label="Funding correction (manual)" value={<><span className="font-bold">Cumulative additions 0.00</span> <span className="font-bold ml-4">Cumulative deductions 0.00</span></>} />
                              <DetailRow label="Interest(cumulative income)" value="Interest balance: 0.00" actions={['Refresh']}/>
                              <DetailRow label="Cumulative deposit" value="0.00" valueColor="text-blue-500" actions={['Deposit record']}/>
                              <DetailRow label="Total withdrawal" value={`$${totalBorrowed.toFixed(2)}`} valueColor="text-blue-500" actions={['Withdrawals record', 'On-time audit']}/>
                              <DetailRow label="Deposit and withdraw difference" value="0.00" valueColor="font-bold"/>
                              <DetailRow label="Bet Today" value="0.00" valueColor="text-blue-500" actions={['Bet record']}/>
                              <DetailRow label="Today's win/losss" value="0.00" valueColor="text-blue-500"/>
                              <DetailRow label="Rewards wallet" value="0.00" actions={['Refresh']}/>
                              <DetailRow label="Event bonus" value="Cumulative rebate: 0.00" actions={['Refresh']}/>
                              <DetailRow label="Rebate settings" value="--" actions={['Modify']}/>
                          </tbody>
                      </table>
                  </div>
               )}

               {activeTab === 'Contact information' && (
                   <div className="bg-white border border-slate-300 shadow-sm p-6 flex gap-8">
                       {/* Left Column: Images */}
                       <div className="w-1/2">
                           <div className="grid grid-cols-2 gap-4">
                               {[0, 1, 2, 3].map((idx) => (
                                   <div key={idx} className="relative group">
                                       <div className="w-full h-32 bg-slate-100 border border-slate-200 rounded-lg flex items-center justify-center overflow-hidden">
                                           {contactData.images[idx] ? (
                                               <img src={contactData.images[idx]} alt={`Doc ${idx+1}`} className="w-full h-full object-cover" />
                                           ) : (
                                               <div className="flex flex-col items-center">
                                                   <Camera className="w-8 h-8 text-slate-300 mb-1" />
                                                   <span className="text-[10px] text-slate-400">Photo {idx + 1}</span>
                                               </div>
                                           )}
                                           {editMode && (
                                               <label className="absolute inset-0 bg-black/50 flex items-center justify-center cursor-pointer opacity-0 group-hover:opacity-100 transition-opacity rounded-lg">
                                                   <Upload className="text-white w-6 h-6" />
                                                   <input type="file" accept="image/*" onChange={(e) => handleImageChange(idx, e)} className="hidden" />
                                               </label>
                                           )}
                                       </div>
                                       {editMode && contactData.images[idx] && (
                                           <button onClick={() => handleRemoveImage(idx)} className="text-red-500 text-[10px] font-bold mt-1 hover:underline w-full text-center">Remove</button>
                                       )}
                                   </div>
                               ))}
                           </div>
                           <p className="text-xs text-gray-500 mt-4">Supported formats: JPG, PNG (Max 800KB)</p>
                       </div>

                       {/* Right Column: Inputs */}
                       <div className="w-1/2 space-y-4">
                           <div className="flex justify-between items-center mb-2">
                               <h3 className="font-bold text-gray-700">Contact Details</h3>
                               <button onClick={() => editMode ? handleSaveUserInfo() : setEditMode(true)} className="text-blue-600 text-xs font-bold border border-blue-200 px-3 py-1 rounded hover:bg-blue-50">
                                   {editMode ? (saving ? 'Saving...' : 'Save All Changes') : 'Edit Contact Info'}
                               </button>
                           </div>
                           
                           <div>
                               <label className="block text-xs font-bold text-gray-500 uppercase mb-1">Residential Address</label>
                               <textarea disabled={!editMode} value={contactData.address} onChange={e=>setContactData({...contactData, address: e.target.value})} className="w-full border p-2 rounded bg-slate-50 text-sm h-20 resize-none disabled:text-gray-500 text-gray-800" placeholder="Enter full address..."></textarea>
                           </div>
                           
                           <div className="grid grid-cols-2 gap-4">
                               <div>
                                   <label className="block text-xs font-bold text-gray-500 uppercase mb-1">Telegram</label>
                                   <input disabled={!editMode} value={contactData.telegram} onChange={e=>setContactData({...contactData, telegram: e.target.value})} className="w-full border p-2 rounded bg-slate-50 text-sm disabled:text-gray-500 text-gray-800" placeholder="@username"/>
                               </div>
                               <div>
                                   <label className="block text-xs font-bold text-gray-500 uppercase mb-1">WhatsApp</label>
                                   <input disabled={!editMode} value={contactData.whatsapp} onChange={e=>setContactData({...contactData, whatsapp: e.target.value})} className="w-full border p-2 rounded bg-slate-50 text-sm disabled:text-gray-500 text-gray-800" placeholder="+1234567890"/>
                               </div>
                           </div>
                       </div>
                   </div>
               )}

               {activeTab === 'Personal information' && (
                   <div className="bg-white border border-slate-300 shadow-sm p-6">
                       <div className="flex justify-between items-center mb-6">
                           <h3 className="font-bold text-gray-700">Client Details</h3>
                           {editMode ? (
                               <div className="flex gap-2">
                                   <button onClick={() => setEditMode(false)} className="text-gray-600 text-sm font-bold border border-gray-300 px-4 py-2 rounded hover:bg-gray-50">Cancel</button>
                                   <button onClick={handleSaveUserInfo} disabled={saving} className="bg-blue-600 text-white text-sm font-bold px-4 py-2 rounded hover:bg-blue-700 flex items-center gap-2">{saving && <Loader2 className="w-4 h-4 animate-spin"/>} Save Changes</button>
                               </div>
                           ) : (
                               <button onClick={() => setEditMode(true)} className="text-blue-600 text-sm font-bold border border-blue-200 px-4 py-2 rounded hover:bg-blue-50">Edit Information</button>
                           )}
                       </div>
                       <div className="grid grid-cols-2 gap-6">
                           <div><label className="block text-xs font-bold text-gray-500 uppercase mb-1">Full Name</label><input disabled={!editMode} value={formData.name} onChange={e=>setFormData({...formData, name:e.target.value})} className="w-full border p-2 rounded bg-gray-50 disabled:text-gray-500 text-gray-800 font-medium"/></div>
                           <div><label className="block text-xs font-bold text-gray-500 uppercase mb-1">Phone Number</label><input disabled={!editMode} value={formData.phone} onChange={e=>setFormData({...formData, phone:e.target.value})} className="w-full border p-2 rounded bg-gray-50 disabled:text-gray-500 text-gray-800 font-medium"/></div>
                           <div><label className="block text-xs font-bold text-gray-500 uppercase mb-1">Email Address</label><input disabled={!editMode} value={formData.email} onChange={e=>setFormData({...formData, email:e.target.value})} className="w-full border p-2 rounded bg-gray-50 disabled:text-gray-500 text-gray-800 font-medium"/></div>
                           <div><label className="block text-xs font-bold text-gray-500 uppercase mb-1">National ID</label><input disabled={!editMode} value={formData.id} onChange={e=>setFormData({...formData, id:e.target.value})} className="w-full border p-2 rounded bg-gray-50 disabled:text-gray-500 text-gray-800 font-medium"/></div>
                       </div>
                   </div>
               )}

               {activeTab === 'Withdrawal account' && (
                   <div className="space-y-4">
                       <div className="flex justify-between items-center">
                           <h3 className="font-bold text-gray-700">Linked Accounts</h3>
                           <div className="flex items-center gap-4">
                               {otpCode && (
                                   <div className="bg-yellow-50 border border-yellow-200 px-3 py-1 rounded text-xs font-bold text-yellow-700 flex items-center gap-2">
                                       <Key className="w-3 h-3" /> OTP: {otpCode}
                                   </div>
                               )}
                               <button onClick={generateOTP} className="bg-slate-100 border border-slate-300 text-slate-700 text-xs font-bold px-3 py-2 rounded hover:bg-slate-200">Generate Application OTP</button>
                               <button onClick={handleAddAccount} className="bg-blue-600 text-white text-xs font-bold px-4 py-2 rounded hover:bg-blue-700">Add Account</button>
                           </div>
                       </div>
                       <div className="bg-white border border-slate-300 shadow-sm rounded">
                           <table className="w-full text-left text-sm">
                               <thead className="bg-slate-50 text-slate-500 uppercase text-xs">
                                   <tr><th className="p-3 border-b">Type</th><th className="p-3 border-b">Account</th><th className="p-3 border-b">Added Time</th><th className="p-3 border-b">Status</th><th className="p-3 border-b">Action</th></tr>
                               </thead>
                               <tbody>
                                   {withdrawalAccounts.map((acc) => (
                                       <tr key={acc.id} className="border-b last:border-0 hover:bg-slate-50">
                                           <td className="p-3 font-bold text-gray-700">{acc.type}</td>
                                           <td className="p-3 font-mono">{acc.account}</td>
                                           <td className="p-3 text-gray-500 text-xs">{acc.time}</td>
                                           <td className="p-3"><span className="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">{acc.status}</span></td>
                                           <td className="p-3"><button onClick={() => handleRemoveAccount(acc.id)} className="text-red-500 hover:underline text-xs font-bold">Remove</button></td>
                                       </tr>
                                   ))}
                                   {withdrawalAccounts.length === 0 && <tr><td colSpan="5" className="p-4 text-center text-gray-400">No withdrawal accounts linked.</td></tr>}
                               </tbody>
                           </table>
                       </div>
                   </div>
               )}

               {activeTab === 'Account transaction' && (
                   <div className="bg-white border border-slate-300 shadow-sm rounded">
                       <table className="w-full text-left text-sm">
                           <thead className="bg-slate-50 text-slate-500 uppercase text-xs">
                               <tr><th className="p-3 border-b">Time</th><th className="p-3 border-b">Type</th><th className="p-3 border-b">Amount</th><th className="p-3 border-b">Status</th></tr>
                           </thead>
                           <tbody>
                               {clientLoans.length > 0 ? clientLoans.map(loan => (
                                   <tr key={loan.id} className="border-b last:border-0 hover:bg-slate-50">
                                       <td className="p-3 text-gray-500 text-xs">{formatDate(loan.timestamp?.seconds)}</td>
                                       <td className="p-3 font-bold text-gray-700">Loan Request</td>
                                       <td className="p-3 font-mono text-blue-600 font-bold">${loan.amount}</td>
                                       <td className="p-3"><span className={`px-2 py-1 rounded text-xs font-bold ${loan.status==='approved'?'bg-green-100 text-green-700':loan.status==='pending'?'bg-orange-100 text-orange-700':'bg-red-100 text-red-700'}`}>{loan.status}</span></td>
                                   </tr>
                               )) : <tr><td colSpan="4" className="p-6 text-center text-gray-400">No transactions found.</td></tr>}
                           </tbody>
                       </table>
                   </div>
               )}

               {activeTab === 'Member\'s log' && (
                   <div className="space-y-4">
                       <div className="flex flex-wrap items-center gap-2 text-xs">
                           <div className="flex border border-slate-300 rounded overflow-hidden">
                               <button className="px-3 py-1 bg-white hover:bg-slate-50 border-r border-slate-300">Day</button>
                               <button className="px-3 py-1 bg-blue-50 text-blue-600 border-r border-slate-300 font-bold">Week</button>
                               <button className="px-3 py-1 bg-white hover:bg-slate-50">Month</button>
                           </div>
                           <div className="flex items-center border border-slate-300 rounded bg-white px-2 py-1 gap-2">
                               <Clock className="w-3 h-3 text-slate-400" />
                               <span className="text-slate-600">{new Date().toISOString().split('T')[0]} - Now</span>
                           </div>
                           <select className="border border-slate-300 rounded px-2 py-1 bg-white text-slate-600 outline-none"><option>Membership Tag</option></select>
                           <select className="border border-slate-300 rounded px-2 py-1 bg-white text-slate-600 outline-none"><option>Please select Membership</option></select>
                           <select className="border border-slate-300 rounded px-2 py-1 bg-white text-slate-600 outline-none"><option>All action items</option></select>
                           <button className="px-3 py-1 border border-slate-300 rounded bg-white hover:bg-slate-50 text-slate-600">Reset</button>
                           <div className="flex-1 text-right">
                               <button className="px-3 py-1 border border-slate-300 rounded bg-white hover:bg-slate-50 text-slate-600 flex items-center gap-1 ml-auto"><Upload className="w-3 h-3" /> Export report</button>
                           </div>
                       </div>

                       <div className="bg-white border border-slate-300 shadow-sm rounded overflow-x-auto">
                           <table className="w-full text-left text-xs whitespace-nowrap">
                               <thead className="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                                   <tr>
                                       <th className="p-3 min-w-[140px]">Operation time</th>
                                       <th className="p-3">Operation Item</th>
                                       <th className="p-3">Operate</th>
                                       <th className="p-3">Before the change</th>
                                       <th className="p-3">After the change</th>
                                       <th className="p-3">Operation result</th>
                                       <th className="p-3">Operation entrance</th>
                                       <th className="p-3">Operation number</th>
                                       <th className="p-3">Operator</th>
                                       <th className="p-3">Client type</th>
                                       <th className="p-3">Browser brand</th>
                                       <th className="p-3">Operating system</th>
                                       <th className="p-3">System version</th>
                                       <th className="p-3">Equipment brand</th>
                                   </tr>
                               </thead>
                               <tbody className="divide-y divide-slate-100">
                                   {memberLogs.length > 0 ? memberLogs.map((log, i) => (
                                       <tr key={log.id} className="hover:bg-slate-50">
                                           <td className="p-3 text-slate-500">{log.time.split('T').join(' ').split('.')[0]}</td>
                                           <td className="p-3 text-slate-700">{log.type}</td>
                                           <td className="p-3 text-blue-500">Non</td>
                                           <td className="p-3 text-slate-400">Non</td>
                                           <td className="p-3 text-slate-400">Non</td>
                                           <td className="p-3"><span className="text-green-600 font-bold">Success</span></td>
                                           <td className="p-3 text-slate-600">Hall</td>
                                           <td className="p-3 text-slate-600">Member</td>
                                           <td className="p-3 text-slate-600">{userId}</td>
                                           <td className="p-3 text-slate-600">iOS sign(v6.5.80)</td>
                                           <td className="p-3 text-slate-600">Mobile Safari v18.6</td>
                                           <td className="p-3 text-slate-600">iOS</td>
                                           <td className="p-3 text-slate-600">iOS 18.6.2</td>
                                           <td className="p-3 text-slate-600">iPhone</td>
                                       </tr>
                                   )) : (
                                       <tr><td colSpan="14" className="p-8 text-center text-gray-400">No records found.</td></tr>
                                   )}
                               </tbody>
                           </table>
                       </div>
                       <div className="text-xs text-slate-500">Total {memberLogs.length}</div>
                   </div>
               )}

               {activeTab === 'Betting statistics' && (
                   <div className="bg-white border border-slate-300 shadow-sm p-12 text-center text-gray-400">
                       <p>No records found for this period.</p>
                   </div>
               )}
            </div>
         </div>
      </div>
   );
};

export default ClientDetailsModal;