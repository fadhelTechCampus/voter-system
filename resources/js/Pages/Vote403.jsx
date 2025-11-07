import React from 'react';

export default function Vote403({ name, reason }) {
  const title =
    reason === 'invalid' ? 'INVALID LINK' :
    reason === 'used' ? 'LINK ALREADY USED' :
    'ALREADY VOTED';

  const subtitle =
    reason === 'invalid'
      ? 'This link does not exist or is broken.'
      : `${name ? name + ',' : ''} you have already used or voted with this link.`;

  return (
    <div className="min-h-screen flex items-center justify-center bg-gray-950 text-white">
      <div className="text-center">
        <div className="text-5xl font-extrabold tracking-wide">403</div>
        <div className="mt-2 text-xl font-semibold">{title}</div>
        <div className="mt-2 opacity-80">{subtitle}</div>
      </div>
    </div>
  );
}
