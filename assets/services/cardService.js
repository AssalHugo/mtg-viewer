export async function fetchCard(uuid) {
    const response = await fetch(`/api/card/${uuid}`);
    if (response.status === 404) return null;
    if (!response.ok) throw new Error('Failed to fetch card');
    const card = await response.json();
    card.text = card.text.replaceAll('\\n', '\n');
    return card;
}

export async function fetchAllCards(page = 1, limit = 20, setCode = '') {
    const response = await fetch(`/api/card/all?page=${page}&limit=${limit}&setCode=${setCode}`);
    if (!response.ok) throw new Error('Failed to fetch cards');
    const result = await response.json();
    return result;
}

export async function fetchCardsByName(name, setCode = '') {
    const response = await fetch(`/api/card/search?name=${name}&setCode=${setCode}`);
    if (!response.ok) throw new Error('Failed to fetch cards');
    const result = await response.json();
    return result;
}

export async function fetchSetCodes() {
    const response = await fetch('/api/card/setcodes');
    if (!response.ok) throw new Error('Failed to fetch set codes');
    const result = await response.json();
    return result;
}
