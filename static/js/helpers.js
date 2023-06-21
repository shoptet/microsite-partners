const slugify = (text) => {
  return (text && text.trim().replace(/\s+/g, '-').toLowerCase());
};

const sha256 = async (message) => {
  const msgUint8 = new TextEncoder().encode(message);
  const hashBuffer = await crypto.subtle.digest('SHA-256', msgUint8);
  const hashArray = Array.from(new Uint8Array(hashBuffer));
  const hashHex = hashArray.map((b) => b.toString(16).padStart(2, '0')).join('');
  return hashHex;
};

export {
  sha256,
  slugify,
};