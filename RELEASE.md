# Release Candidate Checklist & Guide

## Release Process (v1.0.0-rc1)
1. Run static analysis: `phpstan analyse` & `psalm`
2. Run WordPress standards check: `phpcs`
3. Run automated unit & integration verification: `node tests/run-tests.js`
4. Package plugin ZIP artifact: `zip -r liventra-v1.0.0-rc1.zip . -x "*.git*"`
