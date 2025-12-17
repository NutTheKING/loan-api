import { initializeApp } from 'firebase/app';
import {FileText, Copy, Eye, ChevronDown as ChevronDownIcon
} from 'lucide-react';
// --- Utils ---
const exportToCSV = (data, filename) => {
  if (!data || !data.length) return;
  const csvContent = "data:text/csv;charset=utf-8," + 
    Object.keys(data[0]).join(",") + "\n" + 
    data.map(row => Object.values(row).map(val => `"${val}"`).join(",")).join("\n");
  const encodedUri = encodeURI(csvContent);
  const link = document.createElement("a");
  link.setAttribute("href", encodedUri);
  link.setAttribute("download", filename);
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};

const formatDate = (seconds) => {
  if (!seconds) return '-';
  return new Date(seconds * 1000).toLocaleString('en-GB', {
    year: 'numeric', month: '2-digit', day: '2-digit', 
    hour: '2-digit', minute: '2-digit'
  });
};

const copyToClipboard = (text) => {
  const textarea = document.createElement('textarea');
  textarea.value = text;
  document.body.appendChild(textarea);
  textarea.select();
  document.execCommand('copy');
  document.body.removeChild(textarea);
};


const DataTable = ({ loans, onAction, onAddNote, onViewClient, selectedIds, toggleSelection, toggleAll, setInspectingLoan }) => (
  <div className="overflow-x-auto bg-white flex-1">
    <table className="w-full text-left border-collapse ent-table">
      <thead><tr>
    <th className="p-2 w-10 text-center border-r border-[#eceff1]"><input type="checkbox" onChange={toggleAll} checked={loans.length > 0 && selectedIds.length === loans.length} /></th>
      <th className="border-r border-[#eceff1]">Order Details</th>
      <th className="border-r border-[#eceff1]">Member Info</th>
      <th className="border-r border-[#eceff1]">Amount / Term</th>
      <th className="border-r border-[#eceff1]">Risk / Credit</th>
      <th className="border-r border-[#eceff1]">Status</th>
      <th className="text-center">Operate</th></tr></thead>
      <tbody className="text-[11px] text-gray-600">
        {loans.map((loan, idx) => (
          <tr key={loan.id} className={`border-b border-[#cfd8dc] hover:bg-[#e3f2fd] transition-colors ${idx % 2 === 0 ? 'bg-white' : 'bg-[#fafafa]'}`}>
            <td className="p-2 text-center border-r border-[#eceff1]"><input type="checkbox" checked={selectedIds.includes(loan.id)} onChange={() => toggleSelection(loan.id)} /></td>
            <td className="border-r border-[#eceff1] align-top"><div className="flex flex-col gap-1"><span className="text-[#1e88e5] font-medium cursor-pointer hover:underline flex items-center gap-1">{loan.id.slice(0, 10)}... <Copy className="w-3 h-3 text-gray-400 hover:text-blue-500" onClick={() => copyToClipboard(loan.id)} /></span><span className="text-gray-400">{formatDate(loan.timestamp?.seconds)}</span></div></td>
            <td className="border-r border-[#eceff1] align-top"><div className="flex flex-col gap-1 group relative"><button onClick={() => onViewClient(loan.userId)} className="font-bold text-gray-700 hover:text-[#1e88e5] text-left flex items-center gap-1 transition-colors group">{loan.userId.slice(0, 8)} <Eye className="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity text-[#1e88e5]" /></button><span className="text-gray-400 text-[10px]">Real Name: Jose</span></div></td>
            <td className="border-r border-[#eceff1] align-top"><div className="flex flex-col text-right pr-4"><span className="font-bold text-gray-800">${loan.amount.toFixed(2)}</span><span className="text-gray-400 text-[10px]">{loan.term} Months</span></div></td>
            <td className="border-r border-[#eceff1] align-top"><div className="flex justify-between text-[10px] gap-2"><span>Score: <span className="font-bold text-green-600">{loan.creditScore || 720}</span></span><span className="bg-green-100 text-green-700 px-1 rounded">{loan.risk || 'Low'} Risk</span></div></td>
            <td className="border-r border-[#eceff1] align-middle text-center"><span className={`px-2 py-0.5 rounded border text-[10px] font-bold uppercase ${loan.status === 'approved' ? 'bg-green-50 text-green-600 border-green-200' : loan.status === 'rejected' ? 'bg-red-50 text-red-600 border-red-200' : 'bg-orange-50 text-orange-600 border-orange-200'}`}>{loan.status}</span></td>
            <td className="text-center align-middle"><div className="flex flex-col gap-1 items-center"><div className="flex gap-2"><button onClick={() => setInspectingLoan(loan)} className="text-gray-500 hover:text-indigo-600 font-bold text-[10px] border border-gray-200 px-2 py-0.5 rounded">Risk Audit</button></div>{loan.status === 'pending' && (<div className="flex gap-2 mt-1"><button onClick={() => onAction(loan.id, 'approved')} className="text-blue-600 hover:underline">Approve</button><button onClick={() => onAction(loan.id, 'rejected')} className="text-red-600 hover:underline">Reject</button></div>)}<button onClick={() => onAddNote(loan)} className="text-gray-500 hover:text-[#1e88e5] flex items-center gap-1 mt-1"><FileText className="w-3 h-3" /> Remark</button></div></td>
          </tr>
        ))}
      </tbody>
    </table>
  </div>
);

export default DataTable;