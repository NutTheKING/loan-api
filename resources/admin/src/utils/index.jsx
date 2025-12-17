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

export default {exportToCSV, formatDate, copyToClipboard}