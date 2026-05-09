export function useBrowser() {
  const isClient = typeof window !== 'undefined';

  return {
    isClient,
    window: isClient ? window : null,
    document: isClient ? document : null,
  };
}