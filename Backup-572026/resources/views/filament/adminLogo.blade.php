  <style>
      .admin-logo {
          width: 30px;
          height: 30px;
      }

      #admin-logo span {
          font-size: 15px !important;
          color: #fff;
          color: var(--color-white);
          font-weight: 400;
          font-style: italic;
          mix-blend-mode: difference;
          color: var(--color-white, #fff);

          @media (prefers-color-scheme: dark) {
              color: #000;
              color: var(--color-black);
          }
      }

      #admin-logo {
          display: flex;
          align-items: center;
          gap: 10px;

      }
  </style>
  <div id="admin-logo">
      <img src="{{ asset('asset/zcmc.png') }}" alt="ZCMC Logo" class="admin-logo">
      <div style="font-weight: 500;font-size:15px">ZCMC - <span> External Employee Portal</span></div>
  </div>
