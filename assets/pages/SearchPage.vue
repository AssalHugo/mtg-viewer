<script setup>
import { ref, watch, onMounted } from 'vue';
import { fetchCardsByName, fetchSetCodes } from '../services/cardService';

const searchQuery = ref('');
const cards = ref([]);
const loadingCards = ref(false);
const setCodes = ref([]);
const selectedSetCode = ref('');

async function searchCards() {
    if (searchQuery.value.length < 3) {
        cards.value = [];
        return;
    }
    loadingCards.value = true;
    cards.value = await fetchCardsByName(searchQuery.value, selectedSetCode.value);
    loadingCards.value = false;
}

async function loadSetCodes() {
    setCodes.value = await fetchSetCodes();
}

watch([searchQuery, selectedSetCode], searchCards);

onMounted(() => {
    loadSetCodes();
});
</script>

<template>
    <div>
        <h1>Rechercher une Carte</h1>
        <input v-model="searchQuery" placeholder="Entrez le nom de la carte" />
        <select v-model="selectedSetCode">
            <option value="">All Sets</option>
            <option v-for="setCode in setCodes" :key="setCode.setCode" :value="setCode.setCode">
                {{ setCode.setCode }}
            </option>
        </select>
    </div>
    <div class="card-list">
        <div v-if="loadingCards">Loading...</div>
        <div v-else>
            <div class="card" v-for="card in cards" :key="card.id">
                <router-link :to="{ name: 'get-card', params: { uuid: card.uuid } }"> {{ card.name }} - {{
                    card.uuid
                }}
                </router-link>
            </div>
        </div>
    </div>
</template>
