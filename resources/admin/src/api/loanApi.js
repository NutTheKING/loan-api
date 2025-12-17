import axiosInstance from './axiosConfig';

const API_BASE = '/admin';

export const adminApi = {
  // Auth
  login: (data) => axiosInstance.post(`${API_BASE}/login`, data),
  logout: () => axiosInstance.post(`${API_BASE}/logout`),
  
  // Loans
  getLoans: (params) => axiosInstance.get(`${API_BASE}/loans`, { params }),
  getLoan: (id) => axiosInstance.get(`${API_BASE}/loans/${id}`),
  updateLoan: (id, data) => axiosInstance.put(`${API_BASE}/loans/${id}`, data),
  bulkUpdateLoans: (data) => axiosInstance.post(`${API_BASE}/loans/bulk`, data),
  
  // Users
  getUsers: (params) => axiosInstance.get(`${API_BASE}/users`, { params }),
  getUser: (id) => axiosInstance.get(`${API_BASE}/users/${id}`),
  updateUser: (id, data) => axiosInstance.put(`${API_BASE}/users/${id}`, data),
  createUser: (data) => axiosInstance.post(`${API_BASE}/users`, data),
  deleteUser: (id) => axiosInstance.delete(`${API_BASE}/users/${id}`),
  
  // Staff
  getStaff: () => axiosInstance.get(`${API_BASE}/staff`),
  createStaff: (data) => axiosInstance.post(`${API_BASE}/staff`, data),
  updateStaff: (id, data) => axiosInstance.put(`${API_BASE}/staff/${id}`, data),
  deleteStaff: (id) => axiosInstance.delete(`${API_BASE}/staff/${id}`),
  
  // Config
  getConfig: () => axiosInstance.get(`${API_BASE}/config`),
  updateConfig: (data) => axiosInstance.put(`${API_BASE}/config`, data),
  
  // Reports
  getReports: (type) => axiosInstance.get(`${API_BASE}/reports/${type}`),
  exportReport: (type) => axiosInstance.get(`${API_BASE}/reports/export/${type}`, { responseType: 'blob' }),
  
  // Dashboard Stats
  getDashboardStats: () => axiosInstance.get(`${API_BASE}/dashboard/stats`),
  
  // Seed Data
  seedDemoData: () => axiosInstance.post(`${API_BASE}/seed/demo`),
};