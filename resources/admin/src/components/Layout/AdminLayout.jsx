import React, { useState } from 'react';
import { Outlet, useNavigate } from 'react-router-dom';
import EnterpriseHeader from '../Admin/EnterpriseHeader';

const AdminLayout = () => {
  const [activeTab, setActiveTab] = useState('dashboard');
  const [viewMode, setViewMode] = useState('ops');
  const navigate = useNavigate();

  const handleLogout = () => {
    localStorage.removeItem('admin_token');
    localStorage.removeItem('admin_role');
    localStorage.removeItem('admin_user');
    navigate('/login');
  };

  const user = JSON.parse(localStorage.getItem('admin_user') || '{}');

  return (
    <div className="h-screen flex flex-col bg-[#f5f7fa] font-sans text-xs">
      <EnterpriseHeader 
        user={user}
        activeTab={activeTab}
        setActiveTab={setActiveTab}
        onLogout={handleLogout}
        viewMode={viewMode}
        setViewMode={setViewMode}
        pendingCount={0} // You can fetch this from API
      />
      <div className="flex-1 overflow-y-auto">
        <Outlet context={{ setActiveTab }} />
      </div>
    </div>
  );
};

export default AdminLayout;