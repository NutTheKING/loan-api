import React, { useState } from 'react';
import { User, Lock, ChevronDownIcon, Key } from 'lucide-react';

const Login = ({ onLogin }) => {
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');

  const handleSubmit = (e) => {
    e.preventDefault();
    if (username === 'admin73' && password === 'aa123321') {
      onLogin();
    } else {
      setError('Invalid username or password');
    }
  };
 return (
    <div className="min-h-screen admin-login-bg flex items-center justify-center relative overflow-hidden font-sans">
      <div className="absolute inset-0 wave-pattern pointer-events-none"></div>
      
      <div className="bg-white rounded-[4px] shadow-2xl w-[420px] p-10 relative z-10 animate-fade-in">
        <div className="flex flex-col items-center mb-8 mt-4">
           <div className="flex items-center gap-2 mb-2">
              <h1 className="text-2xl font-bold text-gray-700"><span className="text-[#84cc16]">Admin73</span> System</h1>
           </div>
        </div>

        <form onSubmit={handleSubmit} className="space-y-5">
           <div className="space-y-1">
              <div className="relative">
                 <div className="absolute left-3 top-3 text-gray-400"><User className="w-5 h-5" /></div>
                 <input 
                    type="text" 
                    value={username}
                    onChange={(e) => { setUsername(e.target.value); setError(''); }}
                    placeholder="Please enter username" 
                    className="w-full bg-[#e5e7eb] border-none rounded-[4px] py-3 pl-10 pr-4 text-sm text-gray-700 placeholder-gray-400 focus:ring-1 focus:ring-blue-500 outline-none"
                 />
              </div>
              <p className="text-[10px] text-red-500">2-20 characters,support pure English/English and numeric</p>
           </div>

           <div className="space-y-1">
              <div className="relative">
                 <div className="absolute left-3 top-3 text-gray-400"><Lock className="w-5 h-5" /></div>
                 <input 
                    type="password" 
                    value={password}
                    onChange={(e) => { setPassword(e.target.value); setError(''); }}
                    placeholder="Please enter password" 
                    className="w-full bg-[#e5e7eb] border-none rounded-[4px] py-3 pl-10 pr-4 text-sm text-gray-700 placeholder-gray-400 focus:ring-1 focus:ring-blue-500 outline-none"
                 />
              </div>
              <p className="text-[10px] text-red-500">Password cannot be null</p>
           </div>

           {error && <div className="text-xs text-red-600 font-bold text-center bg-red-50 p-2 rounded">{error}</div>}

           <div className="flex items-center gap-2">
              <input type="checkbox" id="remember" className="w-4 h-4 text-blue-500 rounded border-gray-300 focus:ring-blue-500" />
              <label htmlFor="remember" className="text-sm text-blue-500">Remember me</label>
           </div>

           <button type="submit" className="w-full bg-[#3b82f6] hover:bg-blue-600 text-white font-medium py-3 rounded-[4px] transition-colors shadow-lg shadow-blue-200">
              Login
           </button>
        </form>

        <div className="mt-8">
           <div className="flex items-center gap-4 mb-4">
              <div className="h-[1px] bg-gray-200 flex-1"></div>
              <span className="text-xs text-gray-400">Or log in as follows</span>
              <div className="h-[1px] bg-gray-200 flex-1"></div>
           </div>
           <div className="flex flex-col items-center gap-1 cursor-pointer hover:opacity-80 transition-opacity">
              <Key className="w-6 h-6 text-gray-500" />
              <span className="text-xs text-gray-500">Passkey</span>
           </div>
        </div>

        <div className="mt-8 text-center border-t border-gray-100 pt-4">
           <p className="text-[10px] text-gray-400 leading-tight">
              If forgetting the password or for two-step verification, please contact the internal admin. If it remains unsolved, please contact <span className="text-blue-500 underline cursor-pointer">Technical Support</span>
           </p>
        </div>
      </div>
    </div>
  );
};

export default Login;