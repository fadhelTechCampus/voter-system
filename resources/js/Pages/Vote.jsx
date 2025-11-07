import React from 'react';
import { router } from '@inertiajs/react';

export default function Vote({ name, token, googleFormUrl }) {
  const handleFinish = () => {
    router.post('/vote/finish');
  };

  return (
    <div className="min-h-screen bg-gray-950 text-white">
      <div className="max-w-4xl mx-auto p-6">
        <h1 className="text-2xl font-semibold mb-2">Welcome, {name}</h1>
        <p className="opacity-80 mb-6">Your vote is important. Please fill out the form below.</p>

        <div className="rounded-2xl overflow-hidden shadow-lg border border-gray-800">
          <iframe
            src={googleFormUrl}
            width="100%"
            height="760"
            frameBorder="0"
            marginHeight="0"
            marginWidth="0"
            title="Google Form"
          />
        </div>

        <div className="mt-6 flex items-center gap-3">
          <button
            onClick={handleFinish}
            className="px-5 py-2 rounded-2xl bg-emerald-500 hover:bg-emerald-600 font-medium"
          >
            Finish & Logout
          </button>
        </div>
      </div>
    </div>
  );
}
