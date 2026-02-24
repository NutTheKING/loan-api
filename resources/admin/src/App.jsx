import React, { useState, useEffect, useMemo } from 'react';
import {
   initializeApp,
   getAuth,
   signInWithCustomToken,
   signInAnonymously,
   onAuthStateChanged,
   signOut,
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
   orderBy,
} from './firebase-shim';
import { 
  LayoutDashboard, PlusCircle, History, LogOut, ChevronRight, Calculator, Wallet, Landmark, CreditCard, Loader2, Menu, X, 
  Search, RefreshCcw, Download, Settings, FileText, CheckCircle, XCircle, AlertTriangle, User, LayoutGrid, ArrowRightLeft, 
  ShieldCheck, ChevronDown, ChevronsLeft, ChevronsRight, ChevronLeft, Calendar, Filter, MoreHorizontal, Copy, Eye, Briefcase, 
  DollarSign, Smartphone, Globe, Clock, Activity, Lock, Edit2, Users, Shield, FileBarChart, Bell, TrendingUp, TrendingDown, 
  Ban, Unlock, Save, Trash2, Plus, UserPlus, Key, ChevronDown as ChevronDownIcon, Phone, ArrowLeft, RefreshCw, AlertOctagon, Camera, Upload, Timer, KeyRound
} from 'lucide-react';
import Login from './components/auths/Login';
import ClientDetailsModal from './components/modal/clientDetailModal';
import DataTable from './components/share/DataTable';
import LoanManagement from './pages/loanManagement';

// Firebase configuration — read from Vite env vars (resources/admin/.env)
const firebaseConfig = {
   apiKey: import.meta.env.VITE_FIREBASE_API_KEY || 'YOUR_API_KEY',
   authDomain: import.meta.env.VITE_FIREBASE_AUTH_DOMAIN || 'loan-app1123.firebaseapp.com',
   projectId: import.meta.env.VITE_FIREBASE_PROJECT_ID || 'loan-app1123',
   storageBucket: import.meta.env.VITE_FIREBASE_STORAGE_BUCKET || 'loan-app1123.appspot.com',
   messagingSenderId: import.meta.env.VITE_FIREBASE_MESSAGING_SENDER_ID || '377342984195',
   appId: import.meta.env.VITE_FIREBASE_APP_ID || 'YOUR_APP_ID',
   measurementId: import.meta.env.VITE_FIREBASE_MEASUREMENT_ID || 'G-525741884',
};

const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
const db = getFirestore(app);
const appId = 'loan-system-v5'; 


  <><ClientDetailsModal />
  <Login /></>

// --- NEW COMPONENT: Risk Control View ---
const RiskControlView = () => {
    const [autoReject, setAutoReject] = useState(false);
    const [threshold, setThreshold] = useState(600);
    const [saving, setSaving] = useState(false);

    const handleSave = async () => {
        setSaving(true);
        // Simulate save
        await new Promise(r => setTimeout(r, 800));
        setSaving(false);
        alert("Risk settings updated!");
    };

    return (
        <div className="animate-fade-in max-w-2xl mx-auto">
            <div className="bg-white rounded-xl border border-slate-200 p-8 shadow-sm">
                <h3 className="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2"><ShieldCheck className="w-6 h-6 text-indigo-600"/> Risk Control Configuration</h3>
                
                <div className="space-y-6">
                    <div className="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-100">
                        <div>
                            <h4 className="font-bold text-slate-700">Auto-Reject High Risk</h4>
                            <p className="text-xs text-slate-500">Automatically reject applications with credit score below threshold.</p>
                        </div>
                        <div onClick={() => setAutoReject(!autoReject)} className={`w-12 h-6 rounded-full cursor-pointer transition-colors relative ${autoReject ? 'bg-indigo-600' : 'bg-slate-300'}`}>
                            <div className={`w-4 h-4 bg-white rounded-full absolute top-1 transition-all ${autoReject ? 'left-7' : 'left-1'}`}></div>
                        </div>
                    </div>

                    {autoReject && (
                        <div className="animate-fade-in">
                            <label className="block text-xs font-bold text-slate-500 uppercase mb-2">Credit Score Threshold</label>
                            <input type="number" value={threshold} onChange={e=>setThreshold(e.target.value)} className="w-full p-3 border border-slate-200 rounded-lg"/>
                            <p className="text-xs text-slate-400 mt-1">Scores below {threshold} will be auto-rejected.</p>
                        </div>
                    )}

                    <button onClick={handleSave} disabled={saving} className="w-full bg-indigo-600 text-white font-bold py-3 rounded-lg hover:bg-indigo-700 transition-colors flex justify-center items-center gap-2">
                        {saving ? <Loader2 className="animate-spin w-5 h-5"/> : 'Save Configuration'}
                    </button>
                </div>
            </div>
        </div>
    );
};

const EnterpriseHeader = ({ user, activeTab, setActiveTab, onLogout, viewMode, setViewMode, pendingCount }) => (
  <div className="bg-[#1e88e5] text-white transition-colors duration-300">
    <div className="flex justify-between items-center px-4 h-12 text-xs border-b border-blue-400">
      <div className="flex items-center gap-4"><span className="font-bold flex items-center gap-2 text-sm"><LayoutGrid className="w-4 h-4" /> Loan Management System V5.0</span><span className="opacity-80 hidden md:block">Server Time: {new Date().toUTCString()}</span></div>
      <div className="flex items-center gap-4">
         <div className="flex items-center gap-2 bg-blue-700/50 rounded-full px-1 p-0.5 border border-blue-400/30">
            <button onClick={() => setViewMode('ops')} className={`px-3 py-1 rounded-full text-[10px] font-bold transition-all ${viewMode === 'ops' ? 'bg-white text-blue-700 shadow-sm' : 'text-blue-100 hover:text-white'}`}>Operations</button>
            <button onClick={() => setViewMode('admin')} className={`px-3 py-1 rounded-full text-[10px] font-bold transition-all ${viewMode === 'admin' ? 'bg-white text-blue-700 shadow-sm' : 'text-blue-100 hover:text-white'}`}>System Admin</button>
         </div>
        <span className="flex items-center gap-1 font-mono"><User className="w-3 h-3" /> {user?.uid.slice(0,6)}</span>
        <button onClick={onLogout} className="flex items-center gap-1 hover:text-blue-200"><LogOut className="w-3 h-3" /></button>
      </div>
    </div>
    <div className="flex items-end px-2 gap-1 h-10 bg-[#f5f7fa] border-b border-[#cfd8dc]">
      {viewMode === 'ops' ? 
         [{ id: 'loan_management', label: 'Loan Management', count: pendingCount }, { id: 'all_members', label: 'All Members' }, { id: 'risk_control', label: 'Risk Control' }, { id: 'financial_stats', label: 'Financial Statistics' }].map(tab => (
            <button key={tab.id} onClick={() => setActiveTab(tab.id)} className={`px-4 py-2 text-xs font-medium rounded-t-md border-t border-l border-r border-[#cfd8dc] relative -mb-[1px] transition-colors ${activeTab === tab.id ? 'bg-white text-[#1e88e5] border-b-white z-10 font-bold' : 'bg-[#e3f2fd] text-gray-600 border-b-[#cfd8dc] hover:bg-gray-50'}`}>{tab.label}{tab.count > 0 && <span className="ml-2 text-[10px] bg-red-500 text-white px-1 rounded-full">{tab.count}</span>}</button>
         )) : 
         [{ id: 'admin_dashboard', label: 'Dashboard' }, { id: 'user_management', label: 'User Management' }, { id: 'role_permission', label: 'Role & Permission' }, { id: 'loan_config', label: 'Loan Config' }, { id: 'reports', label: 'Reports' }].map(tab => (
            <button key={tab.id} onClick={() => setActiveTab(tab.id)} className={`px-4 py-2 text-xs font-medium rounded-t-md border-t border-l border-r border-[#cfd8dc] relative -mb-[1px] transition-colors ${activeTab === tab.id ? 'bg-white text-purple-600 border-b-white z-10 font-bold' : 'bg-purple-50 text-gray-600 border-b-[#cfd8dc] hover:bg-gray-50'}`}>{tab.label}</button>
         ))
      }
    </div>
  </div>
);

const FilterBar = ({ filters, setFilters, onReset, onExport }) => (
  <div className="bg-white p-3 border-b border-[#cfd8dc] text-xs">
    <div className="flex flex-wrap gap-2 items-center">
      <div className="flex items-center border border-[#cfd8dc] rounded bg-gray-50"><span className="bg-gray-100 px-2 py-1 border-r border-[#cfd8dc] text-gray-500">Application Time</span><input type="date" className="bg-transparent px-2 py-1 outline-none w-32" /><span className="text-gray-400">-</span><input type="date" className="bg-transparent px-2 py-1 outline-none w-32" /></div>
      <div className="flex items-center gap-2"><input placeholder="Member Account / ID" value={filters.search} onChange={(e) => setFilters({...filters, search: e.target.value})} className="border border-[#cfd8dc] rounded px-3 py-1 w-48 outline-none focus:border-[#1e88e5]" /><select value={filters.status} onChange={(e) => setFilters({...filters, status: e.target.value})} className="border border-[#cfd8dc] rounded px-3 py-1 w-32 outline-none bg-white"><option value="all">All Status</option><option value="pending">Pending</option><option value="approved">Paid/Active</option></select></div>
      <button className="bg-[#1e88e5] text-white px-4 py-1 rounded hover:bg-blue-600 flex items-center gap-1"><Search className="w-3 h-3" /> Search</button>
      <button onClick={onReset} className="bg-white border border-[#cfd8dc] text-gray-600 px-4 py-1 rounded hover:bg-gray-50">Reset</button>
    </div>
    <div className="flex justify-between items-center mt-3 pt-2 border-t border-dashed border-[#cfd8dc]"><div className="flex gap-2"><button className="bg-green-600 text-white px-3 py-1 rounded flex items-center gap-1 hover:bg-green-700"><RefreshCcw className="w-3 h-3" /> Refresh / 15s</button><button onClick={onExport} className="bg-white border border-[#cfd8dc] text-gray-600 px-3 py-1 rounded flex items-center gap-1 hover:bg-gray-50"><Download className="w-3 h-3" /> Export Report</button></div></div>
  </div>
);


const AdminApp = ({ user, onLogout }) => {
  const [viewMode, setViewMode] = useState('ops'); 
  const [activeTab, setActiveTab] = useState('loan_management');
  const [loans, setLoans] = useState([]);
  const [staff, setStaff] = useState([]);
  const [selectedIds, setSelectedIds] = useState([]);
  const [filters, setFilters] = useState({ search: '', status: 'all' });
  const [page, setPage] = useState(1);
  const [editingNoteLoan, setEditingNoteLoan] = useState(null);
  const [viewingClient, setViewingClient] = useState(null); 
  const [inspectingLoan, setInspectingLoan] = useState(null);
  const [noteText, setNoteText] = useState('');

  useEffect(() => {
     if (viewMode === 'ops') setActiveTab('loan_management');
     else setActiveTab('admin_dashboard');
  }, [viewMode]);

  useEffect(() => {
    if (!user) return;
    const q = collection(db, 'artifacts', appId, 'public', 'data', 'loans');
    const unsubscribe = onSnapshot(q, (snapshot) => {
      const data = snapshot.docs.map(doc => ({ id: doc.id, ...doc.data() }));
      data.sort((a, b) => (b.timestamp?.seconds || 0) - (a.timestamp?.seconds || 0));
      setLoans(data);
    }, (error) => { console.log("Loans listener error:", error); });
    return () => unsubscribe();
  }, [user]);

  // Fetch Staff
  useEffect(() => {
    if (!user) return;
    const q = collection(db, 'artifacts', appId, 'public', 'data', 'staff');
    const unsubscribe = onSnapshot(q, (snapshot) => {
      const data = snapshot.docs.map(doc => ({ id: doc.id, ...doc.data() }));
      setStaff(data);
    }, (error) => { console.log("Staff listener error:", error); });
    return () => unsubscribe();
  }, [user]);

  const filteredLoans = useMemo(() => {
    return loans.filter(l => {
      const matchSearch = filters.search === '' || l.id.includes(filters.search) || l.userId.includes(filters.search);
      const matchStatus = filters.status === 'all' || l.status === filters.status;
      return matchSearch && matchStatus;
    });
  }, [loans, filters]);

  const handleAction = async (id, status) => {
    try {
       await updateDoc(doc(db, 'artifacts', appId, 'public', 'data', 'loans', id), { status });
       setInspectingLoan(null);
    } catch (e) { console.error(e); }
  };
  
  const handleUpdateLoan = async (id, data) => {
    try { await updateDoc(doc(db, 'artifacts', appId, 'public', 'data', 'loans', id), data); } catch(e) { console.error(e); }
  };

  const handleNoteSave = async () => {
    if (!editingNoteLoan) return;
    try {
      await updateDoc(doc(db, 'artifacts', appId, 'public', 'data', 'loans', editingNoteLoan.id), { adminNotes: noteText });
      setEditingNoteLoan(null);
    } catch (e) { console.error(e); }
  };

  const pendingCount = useMemo(() => loans.filter(l => l.status === 'pending').length, [loans]);

  return (
    <div className="h-screen flex flex-col bg-[#f5f7fa] font-sans text-xs">
      <EnterpriseHeader user={user} activeTab={activeTab} setActiveTab={setActiveTab} onLogout={onLogout} viewMode={viewMode} setViewMode={setViewMode} pendingCount={pendingCount} />
      {viewMode === 'ops' && (
         <>
            {activeTab === 'loan_management' && (
               <>
                  <FilterBar filters={filters} setFilters={setFilters} onReset={() => setFilters({search: '', status: 'all'})} onExport={() => exportToCSV(filteredLoans, 'loans.csv')} />
                  <div className="flex-1 p-2 overflow-hidden flex flex-col">
                     <LoanManagement
                        loans={filteredLoans} 
                        selectedIds={selectedIds} 
                        toggleSelection={(id) => setSelectedIds(prev => prev.includes(id) ? prev.filter(x => x !== id) : [...prev, id])}
                        toggleAll={() => setSelectedIds(prev => prev.length === loans.length ? [] : loans.map(l => l.id))}
                        onAction={handleAction} 
                        onAddNote={(loan) => { setEditingNoteLoan(loan); setNoteText(loan.adminNotes || ''); }} 
                        onViewClient={(userId) => setViewingClient(userId)} 
                        setInspectingLoan={setInspectingLoan} 
                     />
                  </div>
                  <div className="h-10 bg-white border-t border-[#cfd8dc] flex items-center justify-between px-4 text-xs text-gray-600 sticky bottom-0"><div className="flex items-center gap-2"><input type="checkbox" checked={selectedIds.length === filteredLoans.length && filteredLoans.length > 0} onChange={() => setSelectedIds(prev => prev.length === filteredLoans.length ? [] : filteredLoans.map(l => l.id))} /><span>Select All</span><button className="ml-4 bg-white border border-[#cfd8dc] px-2 py-1 rounded hover:bg-gray-50">Batch Operation</button><span className="ml-2 text-gray-400">{filteredLoans.length} items total</span></div><div className="flex items-center gap-1"><button onClick={() => setPage(p => Math.max(1, p - 1))} className="p-1 hover:bg-gray-100 rounded"><ChevronLeft className="w-4 h-4" /></button><span>Page {page}</span><button onClick={() => setPage(p => p + 1)} className="p-1 hover:bg-gray-100 rounded"><ChevronRight className="w-4 h-4" /></button></div></div>
               </>
            )}
            {activeTab === 'financial_stats' && <div className="flex-1 p-6 overflow-y-auto"><FinancialStatsView loans={loans} /></div>}
            {activeTab === 'all_members' && <div className="flex-1 p-6 overflow-y-auto"><AllMembersView loans={loans} /></div>}
            {activeTab === 'risk_control' && <div className="flex-1 flex flex-col items-center justify-center p-8"><RiskControlView /></div>}
         </>
      )}
      {viewMode === 'admin' && (
         <div className="flex-1 p-6 overflow-y-auto">
            {activeTab === 'admin_dashboard' && <AdminDashboardView loans={loans} staff={staff} />}
            {activeTab === 'user_management' && <UserManagementView staff={staff} />}
            {activeTab === 'role_permission' && <RolePermissionView />}
            {activeTab === 'loan_config' && <LoanConfigView />}
            {activeTab === 'reports' && <ReportsView loans={loans} />}
         </div>
      )}
      {editingNoteLoan && (
        <div className="fixed inset-0 bg-black/40 z-50 flex items-center justify-center">
           <div className="bg-white w-[400px] shadow-lg rounded-sm border border-[#cfd8dc]">
              <div className="bg-[#1e88e5] text-white px-3 py-2 font-bold flex justify-between items-center"><span>Backend Remarks / Notes</span><button onClick={() => setEditingNoteLoan(null)}><XCircle className="w-4 h-4" /></button></div>
              <div className="p-4"><textarea value={noteText} onChange={(e) => setNoteText(e.target.value)} className="w-full border border-[#cfd8dc] rounded p-2 h-32 outline-none focus:border-[#1e88e5] text-gray-700" placeholder="Enter notes..." /></div>
              <div className="bg-gray-50 px-4 py-2 flex justify-end gap-2 border-t border-[#cfd8dc]"><button onClick={() => setEditingNoteLoan(null)} className="px-3 py-1 bg-white hover:bg-gray-100 rounded text-gray-600 border">Cancel</button><button onClick={handleNoteSave} className="px-3 py-1 bg-[#1e88e5] text-white hover:bg-blue-600 rounded">Save</button></div>
           </div>
        </div>
      )}
      {viewingClient && <ClientDetailsModal userId={viewingClient} onClose={() => setViewingClient(null)} />}
      {inspectingLoan && <AdminRiskDrawer loan={inspectingLoan} onClose={() => setInspectingLoan(null)} onAction={handleAction} onUpdateLoan={handleUpdateLoan} />}
    </div>
  );
};

// --- SYSTEM ADMIN SUB-VIEWS ---
const AdminDashboardView = ({ loans, staff }) => {
   const totalStaff = staff.length || 0;
   const auditLogs = loans.length * 3 + 150; 
   const systemLoad = Math.min(100, Math.floor(loans.length / 5)); 

   return (
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6 animate-fade-in">
         {[{title: 'Total Staff', val: totalStaff, icon: Users, color: 'blue'}, {title: 'System Load', val: `${systemLoad}%`, icon: Activity, color: 'green'}, {title: 'Audit Logs', val: auditLogs, icon: FileText, color: 'purple'}, {title: 'Alerts', val: '0', icon: ShieldCheck, color: 'emerald'}].map((item, i) => (
            <div key={i} className="bg-white p-6 rounded-xl border border-slate-200 shadow-sm"><div className="flex justify-between"><div><p className="text-xs font-bold text-slate-400 uppercase">{item.title}</p><h3 className="text-2xl font-bold text-slate-800 mt-1">{item.val}</h3></div><div className={`bg-${item.color}-50 p-2 rounded-lg`}><item.icon className={`w-5 h-5 text-${item.color}-600`} /></div></div></div>
         ))}
      </div>
   );
};

const UserManagementView = ({ staff }) => {
   const [isAddOpen, setIsAddOpen] = useState(false);
   const [newName, setNewName] = useState('');
   const [newRole, setNewRole] = useState('Manager');

   const handleAddStaff = async () => {
      if(!newName) return;
      await addDoc(collection(db, 'artifacts', appId, 'public', 'data', 'staff'), {
         name: newName, role: newRole, status: 'Active', joined: new Date().toISOString()
      });
      setIsAddOpen(false); setNewName('');
   };

   const handleDelete = async (id) => {
      await deleteDoc(doc(db, 'artifacts', appId, 'public', 'data', 'staff', id));
   };

   return (
      <div className="bg-white rounded-xl border border-slate-200 overflow-hidden animate-fade-in">
         <div className="px-6 py-4 border-b border-slate-100 flex justify-between items-center"><h3 className="font-bold text-slate-800">Staff Accounts</h3><button onClick={() => setIsAddOpen(true)} className="bg-blue-600 text-white text-xs font-bold px-3 py-2 rounded flex items-center gap-2"><PlusCircle className="w-4 h-4" /> Add User</button></div>
         
         {isAddOpen && (
            <div className="p-4 bg-blue-50 border-b border-blue-100 flex gap-2 items-end">
               <div><label className="text-[10px] font-bold text-slate-500 uppercase">Name</label><input className="border p-2 rounded w-48" value={newName} onChange={e => setNewName(e.target.value)} /></div>
               <div><label className="text-[10px] font-bold text-slate-500 uppercase">Role</label><select className="border p-2 rounded w-48" value={newRole} onChange={e => setNewRole(e.target.value)}><option>Manager</option><option>Admin</option><option>Support</option></select></div>
               <button onClick={handleAddStaff} className="bg-blue-600 text-white px-4 py-2 rounded font-bold">Save</button>
               <button onClick={() => setIsAddOpen(false)} className="bg-white border text-slate-600 px-4 py-2 rounded font-bold">Cancel</button>
            </div>
         )}

         <table className="w-full text-left ent-table"><thead><tr><th>Name</th><th>Role</th><th>Status</th><th>Action</th></tr></thead>
         <tbody>
            {staff.map(s => (
               <tr key={s.id} className="hover:bg-slate-50">
                  <td className="font-medium">{s.name}</td>
                  <td><span className="bg-slate-100 px-2 py-1 rounded text-xs font-bold">{s.role}</span></td>
                  <td><span className="text-green-600 font-bold text-xs">{s.status}</span></td>
                  <td><button onClick={() => handleDelete(s.id)} className="text-red-600 hover:underline">Delete</button></td>
               </tr>
            ))}
            {staff.length === 0 && <tr><td colSpan="4" className="text-center p-4 text-slate-400">No staff members found.</td></tr>}
         </tbody></table>
      </div>
   );
};

const RolePermissionView = () => (
   <div className="bg-white rounded-xl border border-slate-200 p-6 animate-fade-in"><h3 className="font-bold text-slate-800 mb-4">Role Access Matrix</h3><div className="overflow-x-auto"><table className="w-full text-left ent-table"><thead><tr><th>Module</th><th>Admin</th><th>Manager</th></tr></thead><tbody>{['Loan Management', 'User Management', 'Financial Reports'].map(mod => (<tr key={mod}><td className="font-bold">{mod}</td><td><input type="checkbox" checked readOnly className="accent-blue-600" /></td><td><input type="checkbox" checked={mod !== 'User Management'} readOnly className="accent-blue-600" /></td></tr>))}</tbody></table></div></div>
);

// --- UPDATED COMPONENT: LoanConfigView with Data Seeder ---
const LoanConfigView = () => {
   const [rate, setRate] = useState(12);
   const [loading, setLoading] = useState(false);
   const [seedLoading, setSeedLoading] = useState(false);

   useEffect(() => {
      const fetchConfig = async () => {
         try {
            const s = await getDoc(doc(db, 'artifacts', appId, 'public', 'data', 'config', 'settings'));
            if(s.exists()) setRate(s.data().baseInterestRate);
         } catch (e) { console.error(e); }
      };
      fetchConfig();
   }, []);

   const saveConfig = async () => {
      setLoading(true);
      await setDoc(doc(db, 'artifacts', appId, 'public', 'data', 'config', 'settings'), { baseInterestRate: Number(rate) }, { merge: true });
      setLoading(false);
   };

   // --- NEW: Database Seeding Logic ---
   const handleSeedDatabase = async () => {
      if(!window.confirm("This will add demo data (Users, Loans, Staff) to your database. Continue?")) return;
      setSeedLoading(true);
      try {
         const batch = [];
         
         // 1. Create Demo Users
         const users = [
            { id: '1234567', name: 'Jose Silva', phone: '1234567', joined: new Date().toISOString() },
            { id: '9876543', name: 'Maria Santos', phone: '9876543', joined: new Date(Date.now() - 86400000 * 30).toISOString() },
            { id: '5550001', name: 'Carlos Oliveira', phone: '5550001', joined: new Date(Date.now() - 86400000 * 60).toISOString() }
         ];

         for (const u of users) {
            await setDoc(doc(db, 'artifacts', appId, 'public', 'data', 'users_demo', u.id), u);
         }
         const statuses = ['approved', 'pending', 'rejected', 'approved'];
         const types = ['Personal', 'Business', 'Auto', 'Personal'];
         
         for (let i = 0; i < 20; i++) {
            const user = users[Math.floor(Math.random() * users.length)];
            const amount = Math.floor(Math.random() * 45 + 5) * 100; // 500 to 5000
            const status = statuses[Math.floor(Math.random() * statuses.length)];
            const term = [6, 12, 24][Math.floor(Math.random() * 3)];
            
            // Random date in last 60 days
            const date = new Date(Date.now() - Math.floor(Math.random() * 60 * 86400000));
            
            await addDoc(collection(db, 'artifacts', appId, 'public', 'data', 'loans'), {
               userId: user.id,
               amount: amount,
               term: term,
               repaymentTotal: amount * 1.12,
               paidAmount: status === 'approved' ? (Math.random() > 0.5 ? amount * 0.5 : 0) : 0,
               monthlyPayment: (amount * 1.12) / term,
               reason: "Demo loan request generated by seeder.",
               type: types[Math.floor(Math.random() * types.length)],
               status: status,
               timestamp: date, // Using raw date object, Firestore converts it
               risk: Math.random() > 0.8 ? 'High' : 'Low',
               creditScore: Math.floor(Math.random() * (850 - 600) + 600),
               appliedInterest: 0.12,
               adminNotes: status === 'rejected' ? 'Credit score too low' : ''
            });
         }

         // 3. Create Admin Staff
         await setDoc(doc(db, 'artifacts', appId, 'public', 'data', 'staff', 'admin73'), {
            name: 'Super Admin', role: 'Admin', status: 'Active', joined: new Date().toISOString()
         });

         alert("Database populated successfully! Go to the Dashboard or Loan Management to see data.");
      } catch (e) {
         console.error("Seeding failed:", e);
         alert("Error seeding data. " + (e.code === 'permission-denied' ? "Permission denied. Database likely not in Test Mode." : e.message));
      } finally {
         setSeedLoading(false);
      }
   };

   return (
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6 animate-fade-in">
         {/* Config Section */}
         <div className="bg-white rounded-xl border border-slate-200 p-6">
            <h3 className="font-bold text-slate-800 mb-4 flex items-center gap-2"><Settings className="w-4 h-4" /> General Settings</h3>
            <div className="space-y-4">
               <div><label className="text-xs font-bold text-slate-500 uppercase block mb-1">Base Interest Rate (%)</label><input type="number" value={rate} onChange={e => setRate(e.target.value)} className="w-full border border-slate-200 rounded p-2 text-sm" /></div>
               <button onClick={saveConfig} disabled={loading} className="bg-blue-600 text-white text-sm font-bold px-4 py-2 rounded w-full flex justify-center items-center gap-2">{loading && <Loader2 className="animate-spin w-4 h-4"/>} Save Changes</button>
            </div>
         </div>

         {/* Database Management Section */}
         <div className="bg-white rounded-xl border border-slate-200 p-6">
            <h3 className="font-bold text-slate-800 mb-4 flex items-center gap-2"><ShieldCheck className="w-4 h-4 text-orange-500" /> Database Management</h3>
            <div className="bg-orange-50 p-4 rounded-lg border border-orange-100 mb-4">
               <h4 className="text-orange-800 font-bold text-xs uppercase mb-1">Demo Mode</h4>
               <p className="text-orange-700 text-xs">Use this to populate the system with fake users, loans, and stats for demonstration purposes.</p>
            </div>
            <button 
               onClick={handleSeedDatabase} 
               disabled={seedLoading}
               className="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-bold px-4 py-3 rounded w-full flex justify-center items-center gap-2 shadow-sm"
            >
               {seedLoading ? <Loader2 className="animate-spin w-4 h-4 text-orange-500"/> : <><RefreshCcw className="w-4 h-4 text-orange-500" /> Generate Demo Data</>}
            </button>
         </div>
      </div>
   );
};

const ReportsView = ({ loans }) => {
   const handleDownload = (type) => {
      if(type === 'summary') exportToCSV(loans, 'monthly_summary.csv');
      else exportToCSV(loans.filter(l => l.risk === 'High'), 'risk_audit.csv');
   };

   return (
      <div className="bg-white rounded-xl border border-slate-200 overflow-hidden animate-fade-in"><div className="px-6 py-4 border-b border-slate-100"><h3 className="font-bold text-slate-800">System Reports</h3></div><table className="w-full text-left ent-table"><thead><tr><th>Report Name</th><th>Date</th><th>Format</th><th>Action</th></tr></thead><tbody>
         <tr className="hover:bg-slate-50"><td className="font-medium flex items-center gap-2"><FileText className="w-4 h-4 text-slate-400" /> Monthly Summary</td><td>{new Date().toLocaleDateString()}</td><td><span className="bg-slate-100 px-2 py-0.5 rounded text-[10px] font-bold">CSV</span></td><td><button onClick={() => handleDownload('summary')} className="text-blue-600 hover:underline flex items-center gap-1"><Download className="w-3 h-3" /> Download</button></td></tr>
         <tr className="hover:bg-slate-50"><td className="font-medium flex items-center gap-2"><FileText className="w-4 h-4 text-slate-400" /> High Risk Audit</td><td>{new Date().toLocaleDateString()}</td><td><span className="bg-slate-100 px-2 py-0.5 rounded text-[10px] font-bold">CSV</span></td><td><button onClick={() => handleDownload('risk')} className="text-blue-600 hover:underline flex items-center gap-1"><Download className="w-3 h-3" /> Download</button></td></tr>
      </tbody></table></div>
   );
};

const FinancialStatsView = ({ loans }) => {
   const totalRepaid = loans.reduce((acc, curr) => acc + (curr.paidAmount || 0), 0);
   const totalDisbursed = loans.reduce((acc, curr) => acc + curr.amount, 0);
   const totalOutstanding = loans.reduce((acc, curr) => acc + (curr.repaymentTotal - (curr.paidAmount || 0)), 0);

   return (
      <div className="animate-fade-in space-y-6">
         <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {[{t:'Total Repaid', v: `$${totalRepaid.toLocaleString()}`, i:TrendingUp, c:'green'}, {t:'Disbursed', v: `$${totalDisbursed.toLocaleString()}`, i:Wallet, c:'blue'}, {t:'Outstanding', v: `$${totalOutstanding.toLocaleString()}`, i:TrendingDown, c:'red'}].map((s,i)=><div key={i} className="bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4"><div className={`bg-${s.c}-100 p-3 rounded-full`}><s.i className={`w-6 h-6 text-${s.c}-600`} /></div><div><p className="text-xs text-slate-400 font-bold uppercase">{s.t}</p><h3 className="text-2xl font-bold text-slate-800">{s.v}</h3></div></div>)}
         </div>
         <div className="bg-white p-6 rounded-xl border border-slate-200 shadow-sm"><h3 className="font-bold text-slate-800 mb-6">Cash Flow Analysis</h3><div className="h-64 flex items-end gap-2 justify-between px-4">{[40,65,45,80,55,90,70,85,60,75,50,95].map((h, i)=><div key={i} className="w-full flex flex-col justify-end gap-1 group relative"><div style={{height: `${h}%`}} className="bg-blue-600 w-full rounded-t opacity-80"></div><div style={{height: `${h*0.6}%`}} className="bg-green-500 w-full rounded-t opacity-80 -mt-2"></div></div>)}</div></div>
      </div>
   );
};

const AllMembersView = ({ loans }) => {
   const members = useMemo(() => {
      const unique = {};
      loans.forEach(l => {
         if(!unique[l.userId]) {
            unique[l.userId] = { id: l.userId, totalLoans: 0, lastActive: l.timestamp };
         }
         unique[l.userId].totalLoans += 1;
         if(l.timestamp?.seconds > (unique[l.userId].lastActive?.seconds || 0)) unique[l.userId].lastActive = l.timestamp;
      });
      return Object.values(unique);
   }, [loans]);

   return (
      <div className="bg-white rounded-xl border border-slate-200 overflow-hidden animate-fade-in">
         <div className="px-6 py-4 border-b border-slate-100 flex justify-between items-center"><h3 className="font-bold text-slate-800">Member Directory</h3><div className="flex gap-2"><input placeholder="Search..." className="border border-slate-200 rounded px-3 py-1 text-xs outline-none" /></div></div>
         <table className="w-full text-left ent-table"><thead><tr><th>Member Info</th><th>Level</th><th>Last Active</th><th>Status</th><th>Action</th></tr></thead><tbody>
            {members.map((m, i) => (
               <tr key={m.id} className="hover:bg-slate-50">
                  <td><div className="font-bold text-slate-700">{m.id.slice(0, 12)}...</div></td>
                  <td><span className="bg-orange-50 text-orange-600 px-1.5 py-0.5 rounded text-[10px] font-bold">VIP {Math.min(5, Math.floor(m.totalLoans / 2) + 1)}</span></td>
                  <td className="text-slate-500 text-xs">{formatDate(m.lastActive?.seconds)}</td>
                  <td><span className="text-green-600 font-bold text-xs">Normal</span></td>
                  <td><div className="flex gap-2"><button className="text-blue-600 hover:text-blue-800 text-[10px] font-bold border border-blue-200 px-2 py-0.5 rounded">View</button></div></td>
               </tr>
            ))}
         </tbody></table>
      </div>
   );
};

const AdminRiskDrawer = ({ loan, onClose, onAction, onUpdateLoan }) => {
  const [newAmount, setNewAmount] = useState(loan.amount);
  const handleUpdate = async () => { await onUpdateLoan(loan.id, { amount: Number(newAmount) }); onClose(); };
  return <div className="fixed inset-y-0 right-0 w-full md:w-[400px] bg-white shadow-2xl z-50 flex flex-col border-l border-slate-200 animate-slide-in-right"><div className="bg-slate-900 text-white p-5 flex justify-between items-start"><div><h2 className="font-bold text-lg">Risk Audit</h2></div><button onClick={onClose}><XCircle className="w-6 h-6 text-slate-400" /></button></div><div className="p-6 space-y-4"><div className="bg-white p-4 rounded border border-slate-200"><h3 className="font-bold mb-2">Counter-Offer</h3><div className="flex gap-2"><input type="number" value={newAmount} onChange={(e) => setNewAmount(e.target.value)} className="border p-1 w-full rounded" /><button onClick={handleUpdate} className="bg-orange-500 text-white px-3 rounded font-bold">Update</button></div></div><div className="grid grid-cols-2 gap-3"><button onClick={() => onAction(loan.id, 'rejected')} className="bg-white border text-slate-600 py-2 rounded font-bold">Reject</button><button onClick={() => onAction(loan.id, 'approved')} className="bg-emerald-600 text-white py-2 rounded font-bold">Approve</button></div></div></div>;
};

// --- ROOT PORTAL ---
export default function LoanSystemPortal() {
  const [user, setUser] = useState(null);
  const [view, setView] = useState('landing');
  const [loading, setLoading] = useState(true);
  const [authError, setAuthError] = useState(null);

  // New function to bypass auth if user can't fix it
  const handleBypass = () => {
    // Manually set a dummy user state to allow entry
    setUser({ uid: 'guest_bypass_' + Math.floor(Math.random()*1000), isAnonymous: true });
    setAuthError(null);
    setLoading(false);
  };

  useEffect(() => {
    let unsubscribe;
    
    const init = async () => {
      try {
        await signInAnonymously(auth);
      } catch (err) {
        console.error("Firebase Auth Error:", err);
        // Better error message detection
        if (err.code === 'auth/configuration-not-found') {
           setAuthError({
             title: 'Authentication Disabled',
             msg: 'Anonymous Auth is not enabled in Firebase Console.'
           });
        } else if (err.code === 'auth/admin-restricted-operation') {
           setAuthError({
             title: 'Project Restricted',
             msg: 'Your API Key is restricted or Identity Toolkit API is disabled in Google Cloud Console.'
           });
        } else {
           setAuthError({ title: 'Connection Error', msg: err.message });
        }
        setLoading(false);
      }
    };

    unsubscribe = onAuthStateChanged(auth, (u) => { 
       if (u) {
         setUser(u);
         setLoading(false);
         setAuthError(null);
       } else {
         // Waiting for init()
       }
    }, (error) => {
       setAuthError({ title: 'Auth State Error', msg: error.message });
       setLoading(false);
    });

    // Only init if we don't have a user already (prevents double init in strict mode)
    if (!auth.currentUser) {
        init();
    } else {
        setLoading(false);
    }

    return () => {
      if (unsubscribe) unsubscribe();
    };
  }, []); 

  if (authError) {
     return (
        <div className="min-h-screen bg-red-50 flex flex-col items-center justify-center p-6 text-center animate-fade-in font-sans">
  
           <div className="bg-white p-8 rounded-3xl shadow-xl max-w-lg border border-red-100">
              <div className="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                 <AlertTriangle className="w-8 h-8 text-red-600" />
              </div>
              <h2 className="text-2xl font-bold text-slate-800 mb-2">{authError.title}</h2>
              <p className="text-slate-600 mb-6">{authError.msg}</p>
              
              <div className="bg-slate-50 text-left p-4 rounded-xl border border-slate-200 text-sm space-y-2 mb-6">
                 <p className="font-bold text-slate-700">How to Fix:</p>
                 <ol className="list-decimal pl-5 space-y-1 text-slate-600">
                    <li>Go to <a href="https://console.firebase.google.com/" target="_blank" className="text-blue-600 hover:underline">Firebase Console</a> &rarr; Build &rarr; Authentication.</li>
                    <li>Enable <strong>Anonymous</strong> sign-in provider.</li>
                    <li>If "Restricted Operation" error: Go to Google Cloud Console &rarr; APIs & Services &rarr; Credentials and check API key restrictions.</li>
                 </ol>
              </div>
              
              <div className="flex flex-col gap-3">
                 <button onClick={() => window.location.reload()} className="bg-red-600 text-white font-bold py-3 px-8 rounded-xl hover:bg-red-700 transition-colors shadow-lg shadow-red-200">
                    I've Fixed It, Reload App
                 </button>
                 <button onClick={handleBypass} className="bg-white border border-slate-300 text-slate-600 font-bold py-3 px-8 rounded-xl hover:bg-slate-50 transition-colors">
                    Bypass & Enter App (Test Mode Only)
                 </button>
              </div>
           </div>
        </div>
     );
  }

  if (loading) return <div className="h-screen w-full flex flex-col items-center justify-center bg-slate-50 gap-4"><Loader2 className="animate-spin text-indigo-600 w-10 h-10" /></div>;

  return (
    <>
      {view === 'landing' ? (
        <div className="min-h-screen bg-slate-50 flex flex-col items-center justify-center p-6 relative overflow-hidden">
             <div className="bg-white p-10 md:p-14 rounded-[2.5rem] shadow-2xl max-w-4xl w-full text-center relative z-10 border border-slate-100 animate-fade-in">
                <h1 className="text-4xl md:text-5xl font-bold text-slate-900 mb-4">FinCorp Banking Portal V5.0</h1>
                <div className="grid md:grid-cols-2 gap-8 mt-12">
                   <button onClick={() => setView('client_login')} className="group bg-white border border-slate-200 hover:border-indigo-500/50 p-8 rounded-3xl transition-all hover:shadow-xl text-left"><div className="bg-indigo-600 w-14 h-14 rounded-2xl flex items-center justify-center mb-6 text-white"><User className="w-7 h-7" /></div><h3 className="font-bold text-xl text-slate-800">Client App</h3><p className="text-sm text-slate-500 mt-2">Apply for loans & view wallet.</p></button>
                   <button onClick={() => setView('admin_login')} className="group bg-white border border-slate-200 hover:border-slate-800 p-8 rounded-3xl transition-all hover:shadow-xl text-left"><div className="bg-slate-800 w-14 h-14 rounded-2xl flex items-center justify-center mb-6 text-white"><ShieldCheck className="w-7 h-7" /></div><h3 className="font-bold text-xl text-slate-800">Enterprise Backend</h3><p className="text-sm text-slate-500 mt-2">Manage loans, risk & system.</p></button>
                </div>
             </div>
        </div>
      ) : view === 'admin_login' ? (
         <Login onLogin={() => setView('admin')} />
      ) : (
        <AdminApp user={user} onLogout={() => setView('landing')} />
      )}
    </>
  );
}