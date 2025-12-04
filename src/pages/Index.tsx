import { FileText, FolderOpen, Lock, Mail, Download } from "lucide-react";

const Index = () => {
  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">
      {/* Header */}
      <header className="border-b border-slate-200 bg-white/80 backdrop-blur-sm">
        <div className="container mx-auto px-6 py-4 flex justify-between items-center">
          <div className="flex items-center gap-2">
            <div className="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-600 to-purple-600 flex items-center justify-center text-white text-xl">
              📚
            </div>
            <span className="text-xl font-bold text-blue-600">StudentHub</span>
          </div>
          <div className="text-sm text-slate-500">PHP Project Files Ready</div>
        </div>
      </header>

      {/* Hero */}
      <section className="py-20 px-6">
        <div className="container mx-auto max-w-4xl text-center">
          <h1 className="text-5xl font-bold mb-6 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
            StudentHub PHP Project
          </h1>
          <p className="text-xl text-slate-600 mb-8 max-w-2xl mx-auto">
            Complete PHP/MySQL student notes management system with authentication, 
            password reset via Gmail SMTP, and full CRUD operations.
          </p>
          <div className="flex gap-4 justify-center flex-wrap">
            <a 
              href="/php-project/README.md" 
              target="_blank"
              className="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors"
            >
              <Download size={20} />
              View README
            </a>
            <a 
              href="/php-project/sql/database.sql" 
              target="_blank"
              className="inline-flex items-center gap-2 px-6 py-3 bg-slate-200 text-slate-800 rounded-lg font-medium hover:bg-slate-300 transition-colors"
            >
              View SQL Schema
            </a>
          </div>
        </div>
      </section>

      {/* Features */}
      <section className="py-16 px-6 bg-white">
        <div className="container mx-auto max-w-5xl">
          <h2 className="text-3xl font-bold text-center mb-12 text-slate-800">Project Features</h2>
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            {[
              { icon: FileText, title: "Notes CRUD", desc: "Create, edit, delete notes with rich content" },
              { icon: FolderOpen, title: "Module Organization", desc: "Organize notes by subject/module" },
              { icon: Lock, title: "Secure Auth", desc: "Password hashing with bcrypt" },
              { icon: Mail, title: "Email Reset", desc: "PHPMailer + Gmail SMTP" },
              { icon: FileText, title: "CSRF Protection", desc: "Token-based form security" },
              { icon: FolderOpen, title: "Responsive UI", desc: "Mobile-friendly design" },
            ].map((f, i) => (
              <div key={i} className="p-6 rounded-xl border border-slate-200 hover:shadow-lg transition-shadow">
                <div className="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center mb-4">
                  <f.icon className="text-blue-600" size={24} />
                </div>
                <h3 className="font-semibold text-slate-800 mb-2">{f.title}</h3>
                <p className="text-sm text-slate-600">{f.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* File Structure */}
      <section className="py-16 px-6">
        <div className="container mx-auto max-w-3xl">
          <h2 className="text-2xl font-bold mb-6 text-slate-800">📁 Generated Files</h2>
          <div className="bg-slate-900 text-slate-100 rounded-xl p-6 font-mono text-sm overflow-x-auto">
            <pre>{`php-project/
├── README.md              # Setup instructions
├── config/db.php          # Database & email config
├── includes/
│   ├── functions.php      # Helper functions
│   ├── header.php         # Common header
│   └── footer.php         # Common footer
├── assets/
│   ├── css/style.css      # Complete stylesheet
│   └── js/main.js         # JavaScript (modals, validation)
├── sql/database.sql       # MySQL schema
├── index.php              # Landing page
├── register.php           # User registration
├── login.php              # User login
├── forgot_password.php    # Password reset request
├── reset_password.php     # Password reset form
├── dashboard.php          # User dashboard
├── modules.php            # Module CRUD
├── notes.php              # Notes CRUD
└── logout.php             # Logout handler`}</pre>
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer className="py-8 px-6 border-t border-slate-200 text-center text-slate-500 text-sm">
        <p>Download all files from the <code className="bg-slate-100 px-2 py-1 rounded">php-project/</code> folder and run on XAMPP/WAMP</p>
      </footer>
    </div>
  );
};

export default Index;
