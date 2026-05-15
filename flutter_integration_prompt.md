# Flutter Integration Prompt — Neyyah API (نية)

You are implementing the HTTP API layer for **Neyyah (نية)**, a matrimonial Flutter app targeting users in Jordan. The Laravel backend is fully built and running. Your job is to implement all Dio-based API calls, models, and repositories that the BLoC/Cubit layer will consume.

---

## Base Configuration

```
Base URL:     http://YOUR_SERVER/api/v1
Auth:         Bearer token (stored in secure storage after login)
Content-Type: application/json  (except image upload = multipart/form-data)
Accept:       application/json
```

All responses follow this envelope:
```json
{
  "status": "success" | "error",
  "message": "Arabic message string",
  "data": { ... } | [ ... ] | null
}
```

Errors return the same envelope with `"status": "error"` and HTTP 4xx/5xx.

---

## Authentication

### POST `/auth/send-otp`
**Public** — throttled to 5 requests/min per IP.
```json
Request:  { "phone": "+962791234567" }
Response: {
  "data": {
    "code": "123456"   // ← only present in dev/staging, remove in production
  }
}
```

### POST `/auth/verify-otp`
**Public** — Returns token + user object. Creates account if first time.
```json
Request:  { "phone": "+962791234567", "code": "123456" }
Response: {
  "data": {
    "token":  "passport_bearer_token",
    "is_new": true,
    "user":   { ...UserObject }
  }
}
```
→ Save token to FlutterSecureStorage. Save `is_new` to route to profile setup screen.

### POST `/auth/logout`
**Protected**
```
No body. Revokes current token on server.
```

### POST `/auth/refresh-token`
**Protected**
```
No body. Returns: { "data": { "token": "new_token" } }
```
→ Update saved token. Call this when you get 401.

---

## User Object (returned everywhere a user appears)
```json
{
  "id":                    "01HX...",      // ULID string
  "phone":                 "+962791234567",
  "display_name":          "Ahmad",
  "birth_date":            "1995-06-15",
  "age":                   29,             // computed
  "gender":                "male" | "female",
  "city":                  "Amman",
  "bio":                   "...",
  "religiosity_level":     "secular" | "open" | "moderate" | "religious" | "very_religious",
  "education_level":       "none" | "school" | "diploma" | "university" | "postgraduate",
  "income_range":          "low" | "medium" | "high" | "very_high",
  "is_smoker":             false,
  "marriage_timeline":     "asap" | "within_year" | "within_two_years" | "not_sure",
  "completion_pct":        72.5,           // 0–100 float
  "seriousness_score":     0.8,            // 0–1 float
  "is_ready_for_marriage": true,
  "is_verified":           false,
  "last_active_at":        "2026-05-13T10:00:00Z",
  "firebase_uid":          "uid_string",
  "profile_images": [
    {
      "url":         "/assets/admin/uploads/profiles/filename.jpg",
      "blurred_url": "/assets/admin/uploads/profiles/filename_blurred.jpg",
      "sort_order":  1
    }
  ],
  "interests": [
    { "label": "القراءة", "created_at": "..." }
  ],
  "intent_card": {
    "children_intent":         "yes" | "no" | "maybe",
    "open_to_working_partner": true,
    "living_preference":       "independent" | "near_family" | "with_family",
    "target_timeline":         "asap" | "within_year" | "within_two_years" | "not_sure",
    "additional_notes":        "..."
  },
  "privacy_settings": {
    "show_age":                     true,
    "show_city":                    true,
    "show_photos_to_matches_only":  false
  },
  "wallet": {
    "balance":            15,
    "daily_free_reset_at": "2026-05-14T00:00:00Z"
  },
  "match_filters": { ... }  // only present on /profile/me
}
```

---

## Profile Endpoints

### GET `/profile/me`
**Protected** — Returns full UserObject including wallet + matchFilters.

### PUT `/profile/me`
**Protected**
```json
{
  "display_name":          "Ahmad",           // optional fields, only send what changed
  "birth_date":            "1995-06-15",
  "gender":                "male",
  "city":                  "Amman",
  "bio":                   "...",
  "religiosity_level":     "moderate",
  "education_level":       "university",
  "income_range":          "medium",
  "is_smoker":             false,
  "marriage_timeline":     "within_year",
  "is_ready_for_marriage": true,
  "latitude":              31.9539,
  "longitude":             35.9106,
  "firebase_uid":          "uid",
  "interests":             ["القراءة", "الرياضة"]   // replaces all existing interests
}
```

### POST `/profile/setup`
**Protected** — One-shot setup combining profile + intent card + interests.
Same fields as PUT `/profile/me` PLUS:
```json
{
  "children_intent":         "yes",
  "open_to_working_partner": true,
  "living_preference":       "independent",
  "target_timeline":         "within_year",
  "additional_notes":        "..."
}
```

### POST `/profile/me/images`
**Protected** — `multipart/form-data`
```
Field: image  (file, max 5MB, jpg/png/webp)
Max 5 images per user.
```
```json
Response data: {
  "image": {
    "url":         "/assets/...",
    "blurred_url": "/assets/...",
    "sort_order":  2
  }
}
```

### DELETE `/profile/me/images/{order}`
**Protected** — `order` = sort_order integer (1–5)

### PUT `/profile/me/intent-card`
**Protected**
```json
{
  "children_intent":         "yes" | "no" | "maybe",
  "open_to_working_partner": true,
  "living_preference":       "independent" | "near_family" | "with_family",
  "target_timeline":         "asap" | "within_year" | "within_two_years" | "not_sure",
  "additional_notes":        "optional string"
}
```

### PUT `/profile/me/privacy`
**Protected**
```json
{
  "show_age":                    true,
  "show_city":                   true,
  "show_photos_to_matches_only": false
}
```

### GET `/profile/{id}`
**Protected** — View another user's profile.
```json
Response data: {
  "profile":               { ...UserObject (privacy-filtered) },
  "compatibility_score":   0.74,
  "compatibility_factors": {
    "religiosity":  0.9,
    "education":    0.8,
    "timeline":     1.0,
    "smoking":      1.0,
    "income":       0.7
  },
  "proximity": "same_city" | "nearby" | "same_area" | "different_city"
}
```

---

## Cities (Public)

### GET `/cities`
**No auth required.**
```json
Response data: [
  { "id": 1, "name_ar": "عمّان", "name_en": "Amman" },
  { "id": 2, "name_ar": "إربد", "name_en": "Irbid" },
  ...
]
```
→ Cache this list locally. 13 Jordanian cities seeded.

---

## Matching
**Requires profile completion ≥ 50%.**

### GET `/matching/suggestions?page=1&per_page=10`
```json
Response data: {
  "data": [
    {
      "id":                    "user_ulid",
      "profile":               { ...UserObject },
      "compatibility_score":   0.74,
      "compatibility_factors": { ... },
      "proximity":             "same_city",
      "status":                "suggested",
      "suggested_at":          "2026-05-13T10:00:00Z"
    }
  ],
  "total": 42,
  "per_page": 10,
  "current_page": 1,
  "last_page": 5
}
```

### POST `/matching/suggestions/{userId}/view`
**Costs 1 coin.** Returns 402 if insufficient balance.

### POST `/matching/suggestions/{userId}/dismiss`
Removes user from future suggestions permanently.

### GET `/matching/filters`
```json
Response data: {
  "min_age": 22, "max_age": 35,
  "cities": ["Amman"],
  "religiosity_levels": ["moderate"],
  "education_levels": ["university"],
  "no_smokers": true,
  "max_distance_km": null,
  "marriage_timelines": ["within_year"],
  "updated_at": "2026-05-13T10:00:00Z"
}
```

### PUT `/matching/filters`
Same structure as GET response (all fields optional).

---

## Marriage Requests
**Requires profile completion ≥ 50%.**

### POST `/requests`
**Costs 2 coins.** Returns 402 if insufficient balance.
```json
{
  "to_user_id":              "target_user_ulid",
  "reason_for_interest":     "string (optional)",
  "life_goals":              "string (optional)",
  "marriage_expectations":   "string (optional)"
}
```
```json
Response 201: { "data": { ...MarriageRequestObject } }
```

### GET `/requests/incoming?page=1`
### GET `/requests/sent?page=1`
```json
Response data: {
  "data": [ ...MarriageRequestObject ],
  "total": 5, "per_page": 15, "current_page": 1, "last_page": 1
}
```

### GET `/requests/{id}`
### PUT `/requests/{id}/accept`  → Creates a Conversation. Returns `conversation_id`.
### PUT `/requests/{id}/reject`

**MarriageRequestObject:**
```json
{
  "id":                    "ulid",
  "from_user_id":          "ulid",
  "to_user_id":            "ulid",
  "from_user":             { ...UserObject },
  "to_user":               { ...UserObject },
  "reason_for_interest":   "...",
  "life_goals":            "...",
  "marriage_expectations": "...",
  "status":                "pending" | "accepted" | "declined",
  "sent_at":               "2026-05-13T10:00:00Z",
  "responded_at":          null
}
```

---

## Conversations

### GET `/conversations?page=1`
```json
Response data: {
  "data": [ ...ConversationSummaryObject ],
  ...pagination
}
```

### GET `/conversations/{id}`
Returns full ConversationObject.

### GET `/conversations/{id}/questions`
```json
Response data: {
  "questions": [
    {
      "question":       { "id": "ulid", "text_ar": "...", "hint_ar": "...", "category_ar": "...", "sort_order": 1 },
      "my_answer":      { "answer_text": "...", "answered_at": "..." } | null,
      "partner_answer": { "answer_text": "...", "answered_at": "..." } | null
    }
  ],
  "questions_completed_u1": 3,
  "questions_completed_u2": 2,
  "total_questions":        5,
  "is_chat_unlocked":       false
}
```

### POST `/conversations/{id}/questions/{questionId}/answer`
```json
{ "answer_text": "إجابتي على هذا السؤال..." }
```
When BOTH users answer ALL questions → `is_chat_unlocked` becomes `true` → Firebase chat opens.

### PUT `/conversations/{id}/stage`
```json
{ "stage": "ihtimam" }
// Valid stages (must progress forward):
// taaaruf → ihtimam → jiddiyya → family → khitba
```

### PUT `/conversations/{id}/last-activity`
No body. Call this when user opens the conversation to update timestamp.

**ConversationObject / ConversationSummaryObject:**
```json
{
  "id":                       "ulid",
  "user1":                    { ...UserObject },
  "user2":                    { ...UserObject },
  "partner":                  { ...UserObject },    // the other user (not me)
  "stage":                    "taaaruf",
  "questions_completed_u1":   3,
  "questions_completed_u2":   5,
  "is_chat_unlocked":         false,
  "firebase_channel_id":      "channel_id_for_firestore",
  "expires_at":               "2026-05-27T00:00:00Z",
  "last_activity_at":         "2026-05-13T10:00:00Z"
}
```

---

## Coins

### GET `/coins/wallet`
```json
{ "data": { "balance": 15, "daily_free_reset_at": "2026-05-14T00:00:00Z" } }
```

### POST `/coins/claim-daily`
Gives 5 free coins. Fails (422) if already claimed today.
```json
Error data: { "reset_at": "2026-05-14T00:00:00Z" }
```

### POST `/coins/earn`
```json
{ "type": "ad" }   // ad=+1 | share=+2 | invite=+5
// Rate-limited: 10 calls per 60 minutes
```

### GET `/coins/transactions?page=1`
```json
{
  "data": [
    {
      "id": 1,
      "type": "earn" | "spend",
      "amount": 5,
      "description": "عملات يومية مجانية",
      "source": "daily_free" | "ad" | "share" | "invite" | "subscription" | "view_profile" | "send_request",
      "created_at": "..."
    }
  ]
}
```

---

## Subscriptions

### GET `/subscriptions/packages`
```json
{
  "data": [
    {
      "id":               "ulid",
      "name_ar":          "الذهبية",
      "coins_per_month":  100,
      "price_jd":         "4.990",
      "is_popular":       true,
      "features_ar":      ["100 عملة شهرياً", "أولوية في الاقتراحات"],
      "sort_order":       2
    }
  ]
}
```

### GET `/subscriptions/current`
Returns active subscription or `null` in data if none.

### POST `/subscriptions/subscribe`
```json
{ "package_id": "package_ulid", "payment_reference": "knet_txn_id" }
```

---

## Notifications

### GET `/notifications?page=1`
```json
{
  "data": [
    {
      "id":        1,
      "type":      "new_marriage_request" | "request_accepted" | "request_declined" | "chat_unlocked" | "stage_updated" | "partner_answered" | "system",
      "title_ar":  "طلب زواج جديد",
      "body_ar":   "لديك طلب زواج جديد",
      "data":      { "request_id": "ulid" },   // extra context varies by type
      "is_read":   false,
      "created_at": "..."
    }
  ]
}
```

### PUT `/notifications/{id}/read`
### PUT `/notifications/read-all`

---

## Device Tokens (FCM)

### POST `/devices/token`
Call on app start after Firebase.initializeApp().
```json
{ "token": "fcm_token_string", "platform": "android" | "ios" }
```

### DELETE `/devices/token`
Call on logout.
```json
{ "token": "fcm_token_string" }
```

---

## Error Handling

| HTTP Code | Meaning |
|-----------|---------|
| 401 | Token expired / invalid → refresh token or re-login |
| 402 | Insufficient coins |
| 403 | Forbidden (not your resource) |
| 404 | Resource not found |
| 422 | Validation error — `data` may contain field errors |
| 429 | Rate limited |

---

## Coin Costs Summary

| Action | Cost |
|--------|------|
| View a profile | 1 coin |
| Send a marriage request | 2 coins |

## Coin Earn Summary

| Action | Earned |
|--------|--------|
| Daily free claim | 5 coins |
| Watch ad | 1 coin |
| Share app | 2 coins |
| Invite friend | 5 coins |
| New account bonus | 5 coins (auto on register) |

---

## Key Business Rules

1. **Profile completion ≥ 50%** required to access matching and marriage requests. Check `completion_pct` on login.
2. **Opposite gender only** — server enforces; marriage requests to same gender return 422.
3. **Chat unlocks** when BOTH users answer ALL guided questions → `is_chat_unlocked: true` → enable Firestore chat channel using `firebase_channel_id`.
4. **Stage progression** is forward-only: `taaaruf → ihtimam → jiddiyya → family → khitba`.
5. **OTP expires** in 5 minutes. In dev/staging the code is returned in the response.
6. **Interests** are free-text Arabic labels (not a catalog). Max length 60 chars each.
7. **Profile images** are served at the relative path in `url` — prepend base domain.
8. **Blurred images** (`blurred_url`) are shown for users you haven't paid to view yet.
9. **Daily free coins** reset at midnight Asia/Amman time.

---

## Implementation Notes for Flutter

- Use `Dio` with an interceptor that injects `Authorization: Bearer {token}` from secure storage.
- On 401 response: call `POST /auth/refresh-token`, update stored token, retry original request once.
- Image upload: use `FormData` with `MultipartFile.fromFile(...)`.
- All IDs are **ULID strings** (26-char), not integers. Use `String` type.
- `compatibility_score` and `seriousness_score` are floats 0.0–1.0.
- `completion_pct` is float 0.0–100.0.
- Paginated endpoints return `total`, `per_page`, `current_page`, `last_page`.
- `firebase_channel_id` in Conversation is the Firestore document path for real-time chat.
- Push notification payload `data` field varies by `type` — use a union/sealed class pattern.
