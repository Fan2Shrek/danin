<script setup lang="ts">
import { RouterView } from 'vue-router';
import { Head } from '@vueuse/head';

import UserProvider from '@/components/UserProvider.vue';
import NavbarComponent from '@/components/layout/NavbarComponent.vue';
import FooterComponent from '@/components/layout/FooterComponent.vue';
</script>

<template>
    <Head>
        <meta property="og:title" content="Danin" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="https://sruuua.fr" />
        <meta property="og:image" content="https://sruuua.fr/D.png" />
        <meta
            property="og:description"
            content="Danin: Découvrez une nouvelle façon de jouer avec vos amis"
        />
        <meta property="og:site_name" content="Danin" />
        <meta property="og:locale" content="fr_FR" />

        <component :is="'script'" type="application/ld+json">
            { "@context": "https://schema.org", "@graph": [ { "@type": "WebSite", "@id":
            "https://sruuua.fr/", "name": "Danin", "url": "https://sruuua.fr/", "description":
            "Danin: Découvrez une nouvelle façon de jouer avec vos amis", "inLanguage": "fr-FR" } ]
            }
        </component>
    </Head>
    <UserProvider>
        <NavbarComponent />
        <RouterView />
        <FooterComponent />
    </UserProvider>
</template>
