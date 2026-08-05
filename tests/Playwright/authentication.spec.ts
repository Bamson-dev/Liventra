import { test, expect } from '@playwright/test';

test.describe('Liventra SaaS — Authentication & Session Suite', () => {

  test('User Login & Protected SaaS Route Access', async ({ page }) => {
    await page.goto('/login');
    await expect(page).toHaveTitle(/Liventra/);
    
    // Fill credentials & submit
    await page.fill('input[type="email"]', 'admin@liventra.com');
    await page.fill('input[type="password"]', 'password123');
    await page.click('button[type="submit"]');

    // Should navigate to SaaS Dashboard Studio
    await page.waitForURL('/dashboard');
    await expect(page.locator('#liventra-admin-studio')).toBeVisible();
  });

  test('User Account Registration Flow', async ({ page }) => {
    await page.goto('/register');
    await expect(page).toHaveTitle(/Liventra/);
  });

  test('Session Persistence across Page Reloads', async ({ page }) => {
    await page.goto('/dashboard');
    await page.reload();
    await expect(page.locator('#liventra-admin-studio')).toBeVisible();
  });
});
