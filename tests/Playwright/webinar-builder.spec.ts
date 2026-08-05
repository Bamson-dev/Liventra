import { test, expect } from '@playwright/test';

test.describe('Liventra SaaS — 6-Step Webinar Builder Suite', () => {

  test('Create, Configure & Publish Automated Webinar', async ({ page }) => {
    await page.goto('/dashboard');
    await page.click('a[data-nav="create"]');

    // Step 1: Basic Info
    await page.fill('#inp-wizard-title', 'Playwright E2E Masterclass');
    await page.fill('#inp-wizard-presenter', 'Bamidele Matthew');
    await page.click('#btn-wizard-next');

    // Step 2: Playback Security
    await page.click('#btn-wizard-next');

    // Step 3: Registration
    await page.click('#btn-wizard-next');

    // Step 4: Conversion Offer
    await page.click('#btn-wizard-next');

    // Step 5: Audience Chat
    await page.click('#btn-wizard-next');

    // Step 6: Review & Publish
    await page.click('#btn-wizard-next');

    // Verify Published Success Screen & Universal Embed Generators
    await expect(page.locator('.btn-share-webinar')).toBeVisible();
  });
});
