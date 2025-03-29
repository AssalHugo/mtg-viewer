<script setup>
import { ref, watch } from 'vue';
import { fetchCardsByName } from '../services/cardService';

const searchQuery = ref('');
const cards = ref([]);
const loadingCards = ref(false);

async function searchCards() {
    if (searchQuery.value.length < 3) {
        cards.value = [];
        return;
    }
    loadingCards.value = true;
    cards.value = await fetchCardsByName(searchQuery.value);
    loadingCards.value = false;
}

watch(searchQuery, searchCards);
</script>

<template>
    <div>
        <h1>Rechercher une Carte</h1>
        <input v-model="searchQuery" placeholder="Entrez le nom de la carte" />
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
