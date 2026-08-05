import { test, expect } from '@playwright/test';

test.describe('Liventra SaaS — Video Library & Drag & Drop Uploader Suite', () => {

  test('Video Asset Management & Upload Modal', async ({ page }) => {
    await page.goto('/dashboard');
    // Navigate to Video Library tab
    await page.click('a[data-nav="video-library"]');
    await expect(page.locator('#liventra-vid-lib-mount')).toBeVisible();

    // Trigger Upload Video Modal
    await page.click('#btn-upload-video-modal');
    await expect(page.locator('#liventra-drop-zone')).toBeVisible();
  });
});
