<script setup>
import { onMounted, ref } from 'vue';
import { fetchAllCards } from '../services/cardService';

const cards = ref([]);
const loadingCards = ref(true);
const currentPage = ref(1);
const totalPages = ref(1);
const limit = 20;

async function loadCards(page = 1) {
    loadingCards.value = true;
    const result = await fetchAllCards(page, limit);
    cards.value = result.cards;
    totalPages.value = result.totalPages;
    loadingCards.value = false;
}

function nextPage() {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
        loadCards(currentPage.value);
    }
}

function prevPage() {
    if (currentPage.value > 1) {
        currentPage.value--;
        loadCards(currentPage.value);
    }
}

onMounted(() => {
    loadCards();
});
</script>

<template>
    <div>
        <h1>Toutes les cartes</h1>
    </div>
    <div class="card-list">
        <div v-if="loadingCards">Loading...</div>
        <div v-else>
            <div class="card-result" v-for="card in cards" :key="card.id">
                <router-link :to="{ name: 'get-card', params: { uuid: card.uuid } }">
                    {{ card.name }} <span>({{ card.uuid }})</span>
                </router-link>
            </div>
        </div>
    </div>
    <div class="pagination">
        <button @click="prevPage" :disabled="currentPage === 1">Previous</button>
        <span>Page {{ currentPage }} of {{ totalPages }}</span>
        <button @click="nextPage" :disabled="currentPage === totalPages">Next</button>
    </div>
</template>
